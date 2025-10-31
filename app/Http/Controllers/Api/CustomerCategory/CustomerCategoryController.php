<?php

namespace App\Http\Controllers\Api\CustomerCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;

class CustomerCategoryController extends Controller
{
    use ApiResponseTrait;
    protected $customerCategories;
    public function __construct(CustomerCategoryRepositoryInterface $customerCategories)
    {
        $this->customerCategories = $customerCategories;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search', '');
        if(empty($searchFor)) {
            $customerCategories = $this->customerCategories;
            $customerCategories = $type == 'paginated' ? 
            $customerCategories->paginate(10) :
            $customerCategories->all();
        }else{
            $customerCategories = $this->customerCategories->search($searchFor);
        }
        return $this->successCollection($customerCategories, CustomerCategory::class, 'Customer categories retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customerCategory = $request->validate([
            'name' => 'required',
            'discount' => ['required', 'numeric', 'min:0', 'max:100', 'regex:/^\d+(\.\d{1,2})?$/'],
            'status' => 'required|in:0,1', // 0 for inactive, 1 for active
        ]);
        $customerCategory['added_by'] = auth()->user()->id;
        $customerCategory = $this->customerCategories->create($customerCategory);
        return $this->successResource($customerCategory, CustomerCategory::class, 'Customer category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customerCategory = $this->customerCategories->find($id);
        if (!$customerCategory) {
            return $this->error('Customer category not found', 404);
        }
        return $this->successResource($customerCategory, CustomerCategory::class, 'Customer category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customerCategory = $request->validate([
            'name' => 'required',
            'discount' => ['required', 'numeric', 'min:0', 'max:100', 'regex:/^\d+(\.\d{1,2})?$/'],
            'status' => 'required|in:0,1', // 0 for inactive, 1 for active
        ]);
        $customerCategory = $this->customerCategories->update($id, $customerCategory);
        return $this->successResource($customerCategory, CustomerCategory::class, 'Customer category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customerCategory = $this->customerCategories->find($id);
        if (!$customerCategory) {
            return $this->error('Customer category not found', 404);
        }
        $this->customerCategories->delete($id);
        return $this->success(null,'Customer category deleted successfully', 200);
    }
}
