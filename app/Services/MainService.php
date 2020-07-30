<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Repositories\BrandRepository;
use App\Repositories\ColorRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SizeRepository;
use App\Models\Category;
use App\Models\Product;

class MainService
{
    /**
     * MainService constructor.
     * @param Application $app
     * @param ProductRepository $product
     */
    public function __construct(ProductRepository $product)
    {
        $this->product = $product;
    }

    /**
     * @return array
     */
    public function autocomplete()
    {
        $results = array();
        $search = request()->input('term');
        $queries = $this->product->whereAuto($search);
        foreach ($queries as $product) {
            $results[] = ['value' => $product->name];
        }
        return $results;
    }

    /**
     * Get data and count items for filters page.
     * @param $parent
     * @return mixed
     */
    public function getAll($parent)
    {
        $id = $this->product->getParents($parent);
        $data['brand'] = app(BrandRepository::class)->withCount($parent);
        //No need DI for this classes because they called once.
        $data['color'] = app(ColorRepository::class)->withCount($id);
        $data['size'] = app(SizeRepository::class)->withCount($id);
        return $data;
    }


    /**
     * Get data for Home page.
     * @return mixed
     */
    public function getHome()
    {
        $data['brands'] = app(BrandRepository::class)->all();
        $data['latest'] = $this->product->latest();
        $data['products'] = $this->product->product();
       // dd($data);
        return $data;
    }


    /**
     * Get data for search filters.
     * @param $parent
     * @return array
     */
    public function getFilter($parent)
    {
        $data = $this->prepareFilter($parent);
        //dd($data["brand"]);

       
        // $catId = (request()->exists('categ') ? request()->input('categ') : array($parent));
        // $data['banner'] = app(CategoryRepository::class)->whereIn('cat_id', $catId);
        // $data['properties'] = $this->getAll($parent);
        // $data['products'] = $this->pagination($parent);


        //You can use this method as a reference for query part
        //Query to get the product filter based on the category
        $cat = $data["category"];
        //Query to get the type of product and category as well as the details.
        $data = Product::where("cat_id",$data["category"])->whereHas('category',function($query) use($cat){
            $query->where("cat_id",$cat);
        })->get();
       
        //The tranformed array is to convert the collection and store it into an array 
        $transformed = [];
        foreach($data as $key => $product){
            $transformed[$key]["name"] = $product["name"];
            $transformed[$key]["description"] = $product["description"];
            $transformed[$key]["a_img"] = $product["a_img"];
            $transformed[$key]["brands"] = $product->brands->brand; //Products has the realtion of brands 
        }

        //DD to see the output of array for transformed from browser
        dd($transformed);
  
       
        return $data;
    }


    /**
     * Get products details data.
     * @param $slug
     * @param $id
     * @return mixed
     */
    public function getProductInfo($slug, $id)
    {
        $data['latest'] = $this->product->latest();;
        $data['products'] = $this->product->product();
        $data['item'] = $this->product->with('category', 'size', 'color')->findOrFail($id);
        return $data;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getFrameContent($id)
    {
        $data = $this->product->with('category', 'size', 'color')->findOrFail($id);
        return $data;
    }

    /**
     * @param $parent
     * @return array
     */
    public function prepareFilter($parent)
    {
        $data = array(
            'parent' => $parent,
            'size' => (array)request()->input('size'),
            'color' => (array)request()->input('color'),
            'brand' => (array)request()->input('brand'),
            'category' => (array)request()->input('categ')
        );
        return $data;
    }

    /**
     * Get products details.
     * @param $parent
     * @return array
     */
    public function prepareSearch($parent)
    {
        $search = request()->input('search');
        $data = $this->prepareFilter($parent);
        $data['banner'] = app(CategoryRepository::class)->findBy('cat_id', request()->input('categ'));
        $data['properties'] = $this->getAll($parent);
        $data['products'] = $this->product->whereLike($search);
        return $data;
    }


    /**
     * Paginate product.
     * @param $parent
     * @return mixed
     */
    public function pagination($parent)
    {
        $result = $this->product->paginate($parent);
        return $result;
    }
}