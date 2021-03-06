<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelectiveStatusReportMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            "INSERT INTO portal.menu_submenu
                (cod_menu_submenu, ref_cod_menu_menu, cod_sistema,
                nm_submenu, arquivo, title, nivel)
            SELECT
                21474, 73, 2, 'Situação Candidatos',
                'module/Reports/SelectiveStatus', null, 2
            WHERE NOT EXISTS (
                SELECT
                    cod_menu_submenu
                FROM
                    portal.menu_submenu
                WHERE
                    cod_menu_submenu = '21474'
            );

            INSERT INTO pmicontrolesis.menu
                (cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
                ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu)
            SELECT
                21276, 21474, 21271, 'Situação Candidatos', 4,
                'module/Reports/SelectiveStatus', '_self', 1, 23
            WHERE NOT EXISTS (
                SELECT cod_menu FROM pmicontrolesis.menu WHERE cod_menu = '21276'
            );"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            "DELETE FROM TABLE pmicontrolesis.menu WHERE cod_menu = 21276
            DELETE FROM TABLE portal.menu_submenu WHERE cod_menu_submenu = 21474;"
        );
    }
}
