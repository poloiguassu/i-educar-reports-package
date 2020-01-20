<?php

use iEducar\Reports\JsonDataSource;

require_once 'lib/Portabilis/Report/ReportCore.php';
require_once 'App/Model/IedFinder.php';

class SelectiveStageReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource;

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        return 'selective-stage-record';
    }

    /**
     * @inheritdoc
     */
    public function requiredArgs()
    {
        $this->addRequiredArg('processo_seletivo');
        $this->addRequiredArg('ano_selecao');
    }

    /**
     * Retorna o SQL para buscar os dados do relatório principal.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getSqlMainReport()
    {
        $processo_seletivo = $this->args['processo_seletivo'] ?: 0;
        $etapa = $this->args['etapa'] ?: 0;
        $etapa_situacao = $this->args['etapa_situacao'] ?: 0;
        $etapa_data = $this->args['etapa_data'] ?: 0;
        $inicial_min = $this->args['inicial_min'] ?: '';
        $inicial_max = $this->args['inicial_max'] ?: '';

        return
            "SELECT DISTINCT
                public.fcn_upper(p.nome) AS nm_inscrito,
                i.copia_rg, i.copia_cpf, i.copia_residencia,
                i.copia_historico, i.copia_renda, ed.data_etapa as etapa_data,
                ed.horario, 
                CASE WHEN i.area_selecionado IS NOT NULL THEN
                    CASE
                        WHEN i.area_selecionado = 0 THEN 'Turismo'
                        WHEN i.area_selecionado = 1 THEN 'Turismo'
                        WHEN i.area_selecionado = 2 THEN 'Comércio'
                        WHEN i.area_selecionado = 3 THEN 'Comércio'
                        WHEN i.area_selecionado = 4 THEN 'Hospedagem'
                        WHEN i.area_selecionado = 5 THEN 'Eventos'
                        ELSE ''
                    END
                END AS turma,
                CASE WHEN i.area_selecionado IS NOT NULL THEN
                    CASE
                        WHEN i.area_selecionado = 0 THEN '08h as 12h'
                        WHEN i.area_selecionado = 1 THEN '14h as 18h'
                        WHEN i.area_selecionado = 2 THEN '08h as 12h'
                        WHEN i.area_selecionado = 3 THEN '14h as 18h'
                        WHEN i.area_selecionado = 4 THEN '08h as 12h'
                        WHEN i.area_selecionado = 5 THEN '14h as 18h'
                        ELSE ''
                    END
                END AS hora_aula
            FROM
                cadastro.pessoa as p,
                pmieducar.aluno as a,
                pmieducar.inscrito as i
            LEFT JOIN
                pmieducar.inscrito_etapa as et
            ON
                et.ref_cod_inscrito = i.cod_inscrito
            LEFT JOIN
                pmieducar.selecao_etapa_data as ed
            ON
                ed.cod_etapa_data = et.ref_cod_etapa_data
            WHERE
                i.ref_cod_selecao_processo = {$processo_seletivo}
            AND (
                CASE WHEN {$etapa} = 0 THEN TRUE
                ELSE
                    i.cod_inscrito = et.ref_cod_inscrito
                AND
                    et.etapa = '{$etapa}'
                AND
                    et.situacao >= '{$etapa_situacao}'
                END
            )
            AND (
                CASE WHEN {$etapa_data} = 0 THEN TRUE
                ELSE
                    et.ref_cod_etapa_data = '{$etapa_data}'
                END
            )
            AND (
                CASE WHEN '{$inicial_min}' = '' THEN TRUE
                ELSE
                    fcn_upper_nrm(p.nome) >= '{$inicial_min}'
                END
            )
            AND (
                CASE WHEN '{$inicial_max}' = '' THEN TRUE
                ELSE
                    fcn_upper_nrm(p.nome) < '{$inicial_max}'
                END
            )
            AND ref_cod_aluno = a.cod_aluno
            AND p.idpes = a.ref_idpes
            ORDER BY nm_inscrito";
    }
}
