<?php namespace App\Http\Controllers\Marketing;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Components\FileEmail;
use Storage,File;
class ManageEmailController extends Controller
{
    /**
     * path file
     */
    private $file_path = '/resources/pictures/emai_template/';

    /**
     * construct
     */
    public function __construct()
    {
        $this->storage = Storage::disk('s3');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $file_email = FileEmail::paginate(20);
        $no = $file_email->lastItem();

        return view('components.marketing.index')->with(['file_email' => $file_email, 'no' => $no]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $requests)
    {
        $modal = 0;
        $insert = array();
        if ($requests->hasFile('file_picture')) {
            $this->validate($requests,['file_picture'=>'mimes:jpeg,bmp,png,jpg'],['file_picture.mimes'=>'Định dạng file ảnh không đúng']);

            $file_picture = $requests->file('file_picture');
            $name_picture = $this->createFileName($file_picture);

            $this->storage->put(FileEmail::$file_path . $name_picture, File::get($file_picture), 'public');

            $insert['file_picture'] = $name_picture;
        }

        if ($requests->hasFile('file_html')) {
            $this->validate($requests,['file_html'=>'mimes:htm,html'],['file_html.mimes'=>'Định dạng file html không đúng']);

            $file_html = $requests->file('file_html');
            $name_html = $this->createFileName($file_html);

            $this->storage->put(FileEmail::$file_path . $name_html, File::get($file_html), 'public');

            $insert['file_html'] = $name_html;
        }

        if (!empty($insert)) {
            $modal = 1;
            $insert['time_create'] = time();
            FileEmail::insert($insert);
        }

        return redirect(route('modules.manage-email.index'))->with(['modal'=>$modal]);
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

            $data = FileEmail::find($id);

            if ($this->storage->exists(FileEmail::$file_path . $data->file_picture) && $data->file_picture != '') {
                $this->storage->delete(FileEmail::$file_path . $data->file_picture);

            }

            if ($this->storage->exists(FileEmail::$file_path . $data->file_html) && $data->file_html != '') {
                $this->storage->delete(FileEmail::$file_path . $data->file_html);
            }

            FileEmail::destroy($id);

        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return redirect(route('modules.manage-email.index'));
    }

    /**
     * @param $file
     * @return string
     */
    public function createFileName($file)
    {
        $data = pathinfo($file->getClientOriginalName());
        $name = str_slug($data['filename']) . '.' . $data['extension'];

        return $name;
    }
}
