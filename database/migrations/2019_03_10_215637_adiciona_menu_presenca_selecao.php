<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaMenuPresencaSelecao extends Migration
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
                21471, 73, 2, 'Lista Chamada',
                'module/Reports/SelectiveRegistration', null, 2
            WHERE NOT EXISTS (
                SELECT
                    cod_menu_submenu
                FROM
                    portal.menu_submenu
                WHERE
                    cod_menu_submenu = '21471'
            );

            INSERT INTO pmicontrolesis.menu
                (cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
                ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu)
            SELECT
                21273, 21471, 21271, 'Lista Chamada', 2,
                'module/Reports/SelectiveRegistration', '_self', 1, 23
            WHERE NOT EXISTS (
                SELECT cod_menu FROM pmicontrolesis.menu WHERE cod_menu = '21273'
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
            "DELETE FROM TABLE pmicontrolesis.menu WHERE cod_menu = 21273
            DELETE FROM TABLE portal.menu_submenu WHERE cod_menu_submenu = 21471;"
        );
    }
}
