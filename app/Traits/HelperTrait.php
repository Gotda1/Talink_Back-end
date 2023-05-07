<?php
namespace App\Traits; 
use App\Models\RelRolePrivilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
trait HelperTrait{

    /**
     * Evaluate if the user on session has the privilege to make the request
     *
     * @param string $privilege
     * @return boolean
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function hasPrivilege( $privilege )
    {
        $role = Auth::user()->role->code;
        $relRP = RelRolePrivilege::where("role_code", $role)
            ->where("privilege_code", $privilege)
            ->count();

        return $relRP > 0;
    }

    /**
     * Calculate IVa
     *
     * @param float $price
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function calculateTaxes( $price ){
        $percentage = Config::get("app.IVA");
       return $price * ( $percentage / 100 );
    }

    /**
     * Save string base64 as image
     *
     * @param string $folder
     * @param string $subfolder
     * @param string $base64
     * @return string $name name of image
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    function saveImageB64( $folder, $subfolder, $base64)
    {
        if( !$base64 ) return "";

        $name = uniqid() . ".png";
        $path = "$folder/" . $subfolder . "/" . $name;
        $base64 = explode(',', $base64)[1];
        Storage::disk("public")->put($path, base64_decode($base64));
        return $name;           
    }

    /**
     * Save action logs
     *
     * @param String $action
     * @param Model $model
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    function saveLog($action, $model){
        Log::channel("actions")->info(collect([
            "ACTION" => $action,
            [
                "MODEL"  => $model->toArray(), 
                "AUTH"   => Auth::id()
            ]
        ])->toJson());
    }
    
}

?>