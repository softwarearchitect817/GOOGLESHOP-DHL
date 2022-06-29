<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Template;
use ZipArchive;
use File;
class TemplateController extends Controller
{
    protected $extract=false;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth()->user()->can('template.list')) {
            return abort(401);
        }

        $posts=Template::withCount('installed')->latest()->paginate(20);

        return view('admin.template.index',compact('posts'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth()->user()->can('template.upload')) {
            return abort(401);
        }

        $validatedData = $request->validate([
            'file' => 'required|mimes:zip',
        ]);

        ini_set('max_execution_time', '0');
        $name=basename($request->file('file')->getClientOriginalName(), '.'.$request->file('file')->getClientOriginalExtension());


        $zip = new ZipArchive;
        $res = $zip->open($request->file);
        if ($res === TRUE) {
            $zip->extractTo('uploads/tmp');
            $zip->close();
            $this->extract=true;

        } else {
          $this->extract=false;
      }

      if (file_exists('uploads/tmp/'.$name.'/function.php')) {
          include 'uploads/tmp/'.$name.'/function.php';

          if (function_exists('theme_info')) {
             $info=theme_info();
             if (file_exists('uploads/tmp/'.$name.'/function.php')) {
                $theme_assets_root=$info['theme_assets_root'];
                $theme_src_root=$info['theme_src_root'];
                $theme_name=$info['theme_name'];


                $assets_link_path=$info['assets_link_path'];
                $theme_view_path=$info['theme_view_path'];

                File::copyDirectory('uploads/tmp/'.$name.'/'.$theme_assets_root, 'frontend');
                File::copyDirectory('uploads/tmp/'.$name.'/'.$theme_src_root, base_path('resources/views/frontend'));
                File::deleteDirectory('uploads/tmp/'.$name);

                $template= new Template;
                $template->name=$theme_name;
                $template->src_path=$theme_view_path;
                $template->asset_path=$assets_link_path;
                $template->save();

                return response()->json(['Theme Uploaded Successfully']);
             }
             
          }
          else{
            File::deleteDirectory('uploads/tmp/'.$name);
          }
      }
      else{
        if (file_exists('uploads/tmp/'.$name)) {
            File::deleteDirectory('uploads/tmp/'.$name);
        }
      
      }

      $msg['errors']['error']="Something Missing With This Theme";
      return response()->json($msg,401);

     }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth()->user()->can('template.delete')) {
            return abort(401);
        }

        $template= Template::findorFail($id);
        if (file_exists($template->asset_path)) {
            File::deleteDirectory($template->asset_path);
        }

        if (file_exists(base_path('resources/views/'.$template->src_path))) {
          
            File::deleteDirectory(base_path('resources/views/'.$template->src_path));
        }
       $template->delete();

        return back();
       

    }
}
