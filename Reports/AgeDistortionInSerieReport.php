<?php

use iEducar\Reports\JsonDataSource;

require_once 'lib/Portabilis/Report/ReportCore.php';
require_once 'App/Model/IedFinder.php';

class AgeDistortionInSerieReport extends Portabilis_Report_ReportCore
{
    use JsonDataSource;

    /**
     * @inheritdoc
     */
    public function templateName()
    {
        return 'age-distortion-in-serie';
    }

    /**
     * @inheritdoc
     */
    public function requiredArgs()
    {
        $this->addRequiredArg('ano');
        $this->addRequiredArg('instituicao');
        $this->addRequiredArg('curso');
    }

    /**
     * @inheritdoc
     */
    public function getJsonData()
    {
        $queryMainReport = $this->getSqlMainReport();
        $queryHeaderReport = $this->getSqlHeaderReport();
        $queryDatasetReport = $this->getSqlDatasetReport();

        return [
            'main' => Portabilis_Utils_Database::fetchPreparedQuery($queryMainReport),
            'header' => Portabilis_Utils_Database::fetchPreparedQuery($queryHeaderReport),
            'dataset' => Portabilis_Utils_Database::fetchPreparedQuery($queryDatasetReport),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSqlMainReport()
    {
        $situacao = $this->args['situacao'] ?: 0;
        $escola = $this->args['escola'] ?: 0;
        $ano = $this->args['ano'] ?: 0;
        $serie = $this->args['serie'] ?: 0;
        $curso = $this->args['curso'] ?: 0;

        return "
        select serie.nm_serie,
serie.idade_ideal,
curso.nm_curso,
       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and a.ativo = 1

	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) > 4
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) < 18
	    and m.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 16
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_16,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 17
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_17,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 18
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_18,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 19
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_19,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 20
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_20,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 21
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_21,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 22
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_22,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 23
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_23,

       (select coalesce(cast(count(m.cod_matricula) as float), 0)
	   from pmieducar.matricula m
       inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
                                              AND view_situacao.cod_turma = mt.ref_cod_turma
                                              AND view_situacao.sequencial = mt.sequencial
				                              AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where m.ref_ref_cod_serie = serie.cod_serie
	    and (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) = 24
	    and m.ativo = 1
	    and a.ativo = 1
	    and s.ativo = 1
	    and a.ativo = 1) as numero_alunos_24

	from pmieducar.serie
  inner join pmieducar.curso on(serie.ref_cod_curso = curso.cod_curso)
 where (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = serie.cod_serie END)
       and curso.cod_curso = {$curso}
and curso.ativo = 1
and serie.ativo = 1
order by serie.nm_serie;
        ";
    }

    public function getSqlDatasetReport()
    {
        $situacao = $this->args['situacao'] ?: 0;
        $escola = $this->args['escola'] ?: 0;
        $serie = $this->args['serie'] ?: 0;
        $curso = $this->args['curso'] ?: 0;
        $ano = $this->args['ano'] ?: 0;

        return "
        select


cast(count(m.cod_matricula) as float) as num_alunos,
       (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) as idade,
       s.nm_serie,
       s.etapa_curso,
       s.idade_ideal,
(select cast(count(m.cod_matricula) as float) as num_alunos

	   from pmieducar.matricula m
	   inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
	                                          AND view_situacao.cod_turma = mt.ref_cod_turma
	                                          AND view_situacao.sequencial = mt.sequencial
				                  AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie  on (m.ref_ref_cod_serie = serie.cod_serie)
	  where  (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and serie.nm_serie = s.nm_serie
	    and (CASE WHEN {$serie}  = 0 THEN true ELSE {$serie}  = m.ref_ref_cod_serie END)
	    and (CASE WHEN  {$serie} = 0 THEN true ELSE {$serie} = serie.cod_serie END)
	    and m.ref_cod_curso = {$curso}
 	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) > 4
 	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) < 18
	    and m.ano = {$ano}
	    and m.ativo = 1
	    and serie.ativo = 1
	    and a.ativo = 1) as num_alunos_serie

	   from pmieducar.matricula m
	   inner join pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
	   inner join relatorio.view_situacao on (view_situacao.cod_matricula = m.cod_matricula
	                                          AND view_situacao.cod_turma = mt.ref_cod_turma
	                                          AND view_situacao.sequencial = mt.sequencial
				                  AND view_situacao.cod_situacao = {$situacao})
	   inner join pmieducar.aluno a on(m.ref_cod_aluno = a.cod_aluno)
	   inner join cadastro.fisica f on(a.ref_idpes = f.idpes)
	   inner join pmieducar.serie s on (m.ref_ref_cod_serie = s.cod_serie)
	  where  (CASE WHEN  0 = {$escola} THEN true ELSE {$escola} = m.ref_ref_cod_escola END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = m.ref_ref_cod_serie END)
	    and (CASE WHEN  0 = {$serie} THEN true ELSE {$serie} = s.cod_serie END)
	    and m.ref_cod_curso = {$curso}
	    and m.ano = {$ano}
	    and m.ativo = 1
 	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) > 4
 	    and (({$ano}) - (cast(to_char(f.data_nasc,'yyyy') as integer))) < 18
	    and a.ativo = 1
	    and s.ativo = 1
	    group by idade, nm_serie, idade_ideal, s.etapa_curso
	    order by nm_serie, idade
        ";
    }
}
