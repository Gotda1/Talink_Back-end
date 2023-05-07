<?php

namespace App\Imports;

use App\Models\Import;
use App\Models\Privilege;
use App\Models\RelRolePrivilege;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PrivilegesImports implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PrivilegeSheet(),
            new AdminPrivilegeSheet(),
            new RecluiterPrivilegeSheet(),
            new ContbPrivilegeSheet(),
            new GtePrivilegeSheet(),
        ];
    }
}

class PrivilegeSheet implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row["code"])) return;
        return new Privilege([
            "code"        => $row["code"],
            "name"        => $row["name"],
            "description" => $row["description"],
            "father"      => $row["father"],
            "asignable"   => $row["asignable"],
            "order"       => $row["order"]
        ]);
    }
}

class AdminPrivilegeSheet implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row["code"])) return;
        return new RelRolePrivilege([
            "role_code"      => "ADMN",
            "privilege_code" => $row["code"]
        ]);
    }
}

class RecluiterPrivilegeSheet implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row["code"])) return;
        return new RelRolePrivilege([
            "role_code"      => "VTAS",
            "privilege_code" => $row["code"]
        ]);
    }
}

class ContbPrivilegeSheet implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row["code"])) return;
        return new RelRolePrivilege([
            "role_code"      => "CONT",
            "privilege_code" => $row["code"]
        ]);
    }
}

class GtePrivilegeSheet implements ToModel, WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!isset($row["code"])) return;
        return new RelRolePrivilege([
            "role_code"      => "GTE",
            "privilege_code" => $row["code"]
        ]);
    }
}

