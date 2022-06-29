<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable,HasRoles,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getPermissionGroup()
    {
        return $permission_groups = DB::table('permissions')
            ->select('group_name')
            ->groupBy('group_name')
            ->get();
    }
    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    public function term()
    {
        return $this->hasMany('App\Term', 'user_id', 'id');
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    public function usermeta()
    {
        return $this->hasOne('App\Usermeta')->where('type','content');
    }

    public function user_domain()
    {
        return $this->belongsTo('App\Domain','domain_id','id');
    }

    public function orders()
    {
        return $this->hasMany('App\Order','customer_id','id');
    }
    public function orders_complete()
    {
        return $this->hasMany('App\Order','customer_id','id')->where('status','completed');
    }

    public function orders_processing()
    {
        return $this->hasMany('App\Order','customer_id','id')->where('status','!=','completed')->where('status','!=','canceled');
    }


    public function user_plan()
    {
        return $this->hasOne('App\Models\Userplan','user_id','id')->orderBy('id','DESC')->where('status',1)->with('plan_info');
    }

    public function customers()
    {
       return $this->hasMany('App\User','created_by','id');
    }

    public function userlimit(){
        return $this->hasOne('App\Models\Userplanmeta');
    }
    
    public function article(){
        return $this->hasMany('App\Models\Article');
    }
   
  
}