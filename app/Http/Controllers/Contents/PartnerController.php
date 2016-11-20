<?php namespace App\Http\Controllers\Contents;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Components\Partner;
use App\Http\Requests\PartnerRequest;
use App\Mytour\Classes\Images\UploadImage;

use Config, Input, Log;

class PartnerController extends Controller
{

    private $array_type = array(1 => "Đối tác ngân hàng",
                                2 => "Đối tác du lịch",
                                3 => "Đối tác thương mại điện tử",
                                4 => "Đối tác nhà mạng viễn thông",
                                5 => "Đối tác truyền thông",
                                6 => "Đối tác nhà hàng");

    /**
     * construct
     */
    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, Partner $partner)
    {
        $pn_type = $request->get('pn_type', 0);
        $pn_name = $request->get('pn_name', '');

        $form = array('open'    => ['url' => route('modules.partners.index'), 'class' => 'form-inline', 'method' => 'GET'],
                      'pn_type' => ['name' => 'pn_type', 'array' => $this->array_type, 'value' => $pn_type, 'label' => 'Chọn đối tác:'],
                      'pn_name' => ['name' => 'pn_name', 'value' => $pn_name, 'label' => 'Tên đối tác:'],
                      'submit'  => ['name' => 'Search']);


        $query_partner = $partner->select(array('*'));

        if ($pn_type > 0) {
            $query_partner->where('pn_type', '=', $pn_type);
        }

        if ($pn_name != '') {
            $query_partner->where('pn_name', 'LIKE', "%" . $pn_name . "%");
        }

        $all_partner = $query_partner->paginate(NUM_PER_PAGE);

        if ($pn_type > 0 && $pn_name != '') {
            $all_partner->appends($request->query);
        }

        $array_type = $this->array_type;
        return view('components.modules.partner.index', compact('all_partner', 'array_type', 'form'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $form = array('open'      => ['url' => route('modules.partners.store'), 'role' => 'form', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true],
                      'pn_type'   => ['name' => 'pn_type', 'array' => $this->array_type, 'label' => 'Chọn đối tác'],
                      'pn_name'   => ['name' => 'pn_name', 'label' => 'Tên đối tác'],
                      'pn_link'   => ['name' => 'pn_link', 'label' => 'URL'],
                      'pn_logo'   => ['name' => 'pn_logo', 'label' => 'Logo', 'help' => '(Dung lượng tối đa 500 Kb có tỉ lệ 4x3)'],
                      'pn_active' => ['name' => 'pn_active', 'value' => 1, 'class' => 'minimal', 'label' => 'Active', 'check' => true],
                      'pn_info'   => ['name' => 'pn_info', 'label' => 'Thông tin đối tác', 'rows' => 3, 'cols' => 10],
                      'submit'    => ['name' => 'Thêm mới']);

        return view('components.modules.partner.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(PartnerRequest $request)
    {
        $data = $request->all();

        $data['pn_logo'] = '';
        if ($request->hasFile('pn_logo')) {

            $imageProcessing = new UploadImage();
            $imageProcessing->make($this->optionImage())->save($request->file('pn_logo'));

            if(count($imageProcessing->error()) == 0){
                $data['pn_logo'] = $imageProcessing->fileName();
            }
        }

        $this->partner->create($data);
        return redirect()->route('modules.partners.create')->with(array('status' => 'Thêm mới thành công'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            $partner = Partner::findOrFail($id);
            $form    = array('model'     => ['url' => route('modules.partners.update', $partner->pn_id), 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PUT', 'files' => true],
                             'pn_type'   => ['name' => 'pn_type', 'array' => $this->array_type, 'label' => 'Chọn đối tác'],
                             'pn_name'   => ['name' => 'pn_name', 'label' => 'Tên đối tác'],
                             'pn_link'   => ['name' => 'pn_link', 'label' => 'URL'],
                             'pn_logo'   => ['name' => 'pn_logo', 'label' => 'Logo', 'help' => '(Dung lượng tối đa 1000 Kb có tỉ lệ 4x3)'],
                             'pn_active' => ['name' => 'pn_active', 'value' => 1, 'class' => 'minimal', 'label' => 'Active'],
                             'pn_info'   => ['name' => 'pn_info', 'label' => 'Thông tin đối tác', 'rows' => 3, 'cols' => 10],
                             'submit'    => ['name' => 'Cập nhật']);

            return view('components.modules.partner.edit', compact('form', 'partner'));

        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response('Menu not found!', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, PartnerRequest $request)
    {
        try {
            $partner = Partner::findOrFail($id);

            $logo_old = $partner->pn_logo;

            if ($request->hasFile('pn_logo')) {

                $imageProcessing = new UploadImage();
                $imageProcessing->make($this->optionImage())->save($request->file('pn_logo'));

                if (count($imageProcessing->error()) == 0) {
                    $imageProcessing->delete($logo_old);
                    $partner->pn_logo = $imageProcessing->fileName();
                }
            }

            $partner->pn_type   = $request->pn_type;
            $partner->pn_name   = $request->pn_name;
            $partner->pn_link   = $request->pn_link;
            $partner->pn_info   = $request->pn_info;
            $partner->pn_active = $request->get('pn_active', 0);

            $partner->save();

            return redirect()->route('modules.partners.index')->with(array('status' => 'Sửa thành công.'));

        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response('Menu not found!', 404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $partner = Partner::findOrFail($id);

            $imageProcessing = new UploadImage();
            $imageProcessing->make($this->optionImage())->delete($partner->pn_logo);

            $partner->delete();

            return redirect()->route('modules.partners.index')->with(array('status' => 'Xóa thành công.'));

        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response('Menu not found!', 404);
        }
    }

    /**
     * @return array
     */
    public function optionImage()
    {
        return array('path'        => Config::get('image_config.pathPartner'),
                     'prefix_size' => Config::get('image_config.sizePartner'),
                     'first_name'  => Config::get('image_config.namePartner'),);
    }
}
