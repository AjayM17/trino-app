<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\Product;
use App\Repositories\CrudRepository;
use App\Repositories\OrderRepository;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class BackendController extends Controller
{

    /**
     * Create a name for table.
     */

    protected $crud;


    /**
     * @param CrudRepository $CrudRepo
     */
    public function __construct(CrudRepository $CrudRepo)
    {
        $this->crud = $CrudRepo;
    }

    /**
     * Show the home page to the user.
     *
     * @return Response
     */

    public function dashboard()
    {
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            $title = 'Admin Dashboard';
            $totalUsers =  User::count();
            $totalProducts = Product::count();
        } else {
            $title = 'User Dashboard';
        }
        return view('backend/dashboard', compact('title','totalUsers','totalProducts'));
    }

    /**
     * Show main categories
     * @return View
     */
    public function brand()
    {
        $filter = $this->crud->brandsFilter();
        $grid = $this->crud->brandsGrid();
        $title = $this->crud->getBrandTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    /**
     * Edit Main Category
     * @return string|View
     */
    public function brandsEdit()
    {
        if (request()->get('do_delete') == 1) return "not the first";
        $edit = $this->crud->brandsEdit();
        $title = $this->crud->getBrandTable();
        return view('backend/content', compact('edit', 'title'));
    }

    /**
     * Show main categories
     * @return View
     */
    public function category()
    {
        $filter = $this->crud->catFilter();
        $grid = $this->crud->catGrid();
        $title = $this->crud->getCatTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    /**
     * Edit Main Category
     * @return string|View
     */
    public function categoryEdit()
    {
        if (request()->get('do_delete') == 1) return "not the first";
        $edit = $this->crud->catEdit();
        $title = $this->crud->getCatTable();
        return view('backend/content', compact('edit', 'title'));
    }



    /**
     * Show products
     * @return View
     */
    public function products()
    {
        $filter = $this->crud->productsFilter();
        $grid = $this->crud->productsGrid();
        $title = $this->crud->getProductTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    /**
     * Edit Products
     * @return string|View
     */
    public function productsEdit()
    {
        if (request()->get('do_delete') == 1) return "not the first";
        $edit = $this->crud->productsEdit();
        $title = $this->crud->getProductTable();
        return view('backend/content', compact('edit', 'title'));
    }

    /**
     * Show User profile
     * @return View
     */
    public function profile()
    {
        $grid = $this->crud->profileGrid();
        $title = auth()->user()->name;
        return view('backend/content', compact('grid', 'title'));
    }

    /**
     * Edit User Profile
     * @return View
     */
    public function profileEdit()
    {
        $edit = $this->crud->profileEdit();
        $title = auth()->user()->name;
        return view('backend/content', compact('edit', 'title'));
    }

    /**
     * Show all orders to admins
     * @return View
     */
    public function orders()
    {
        $filter = $this->crud->ordersFilter();
        $grid = $this->crud->ordersGrid();
        $title = $this->crud->getOrderTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    // show weekly order

    public function weeklyOrders()
    {
        $filter = $this->crud->weeklyOrdersFilter();
        $grid = $this->crud->weeklyOrdersGrid();
        $title = $this->crud->getWeeklyOrderTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    // show monthly order

    public function monthlyOrders()
    {
        $filter = $this->crud->monthlyOrdersFilter();
        $grid = $this->crud->monthlyOrdersGrid();
        $title = $this->crud->getMonthlyOrderTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    // show daily order

    public function dailyOrders()
    {
        $filter = $this->crud->dailyOrdersFilter();
        $grid = $this->crud->dailyOrdersGrid();
        $title = $this->crud->getDailyOrderTable();
        return view('backend/content', compact('filter', 'grid', 'title'));
    }

    /**
     * Edit Orders
     * @return string|View
     */
    public function ordersEdit()
    {
        if (request()->get('do_delete') == 1) return "not the first";
        $edit = $this->crud->ordersEdit();
        $title = $this->crud->getOrderTable();
        return view('backend/content', compact('edit', 'title'));
    }

    /**
     * Show customer orders
     * @return View
     */
    public function userOrders()
    {
        $grid = $this->crud->UserOrdersGrid();
        $title = $this->crud->getOrderTable();
        return view('backend/content', compact('grid', 'title'));
    }

    /**
     * Edit customer orders
     * @return View
     */
    public function userOrdersEdit(OrderRepository $order, GateContract $gate)
    {
        if (request()->has('update')) {
            $result = $order->findBy('id', request()->input('update'));
        } else {
            $result = $order->findOrFail(request()->all());
        }
        //$this->authorize('view-resource', $order);
        //authorize via AuthorizesRequests trait.
        if ($gate->denies('view-resource', $result)) {
            return redirect('backend/profile')
                ->withErrors('Your are not autorized to view resources');
        }
        $edit = $this->crud->UsersOrdersEdit();
        $title = $this->crud->getOrderTable();
        return view('backend/content', compact('edit', 'title'));
    }
}