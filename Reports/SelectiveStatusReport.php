<?php

use iEducar\Reports\JsonDataSource;

require_once 'lib/Portabilis/Report/ReportCore.php';
require_once 'App/Model/IedFinder.php';

class SelectiveStatusReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource;

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        return 'selective-document-phone';
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
        $etapa_1 = $this->args['etapa_1'] ?: 0;
        $etapa_2 = $this->args['etapa_2'] ?: 0;
        $inicial_min = $this->args['inicial_min'] ?: '';
        $inicial_max = $this->args['inicial_max'] ?: '';

        return
            "SELECT DISTINCT
                public.fcn_upper(p.nome) AS nm_inscrito,
                CASE WHEN f.idpes IS NOT NULL THEN
                    CASE
                        WHEN f.tipo = 0 THEN f.fone
                        WHEN f.tipo = 1 THEN f.fone
                        WHEN f.tipo = 2 THEN f.fone
                        WHEN f.tipo = 3 THEN f.fone
                        WHEN f.tipo = 4 THEN f.fone
                        ELSE null
                    END
                END AS telefone_1,
                CASE WHEN f.idpes IS NOT NULL THEN
                    CASE
                        WHEN f.tipo = 5 THEN f.fone
                        ELSE null
                    END
                END AS telefone_2
            FROM
                pmieducar.inscrito as i,
                pmieducar.inscrito_etapa as e,
                cadastro.pessoa as p,
                pmieducar.aluno as a
            LEFT JOIN
                cadastro.fone_pessoa as f
            ON
                f.idpes = a.ref_idpes
            WHERE
                i.ref_cod_selecao_processo = {$processo_seletivo}
            AND (
                CASE WHEN {$etapa_1} = 0 THEN TRUE
                ELSE
                    e.etapa = 1
                AND
                    e.situacao >= {$etapa_1}
                END
            )
            AND (
                CASE WHEN {$etapa_2} = 0 THEN TRUE
                ELSE
                    e.etapa = 2
                AND
                    e.situacao = {$etapa_2}
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
            AND i.cod_inscrito = e.ref_cod_inscrito
            AND ref_cod_aluno = a.cod_aluno
            AND p.idpes = a.ref_idpes
            GROUP BY nm_inscrito, f.idpes, f.tipo, f.fone ORDER BY nm_inscrito";
    }
}
