<?php

use iEducar\Modules\Inscritos\Model\AvaliacaoEtapa;

require_once 'lib/Portabilis/Controller/ReportCoreController.php';
require_once 'Reports/Reports/SelectiveDocumentReport.php';

class SelectiveDocumentController extends Portabilis_Controller_ReportCoreController
{
    /**
     * @var int
     */
    protected $_processoAp = 21473;

    /**
     * @var string
     */
    protected $_titulo = 'Lista de chamada Processo Seletivo';

    /**
     * @var int
     */
    public $periodo;

    /**
     * @var int
     */
    public $cod_servidor_funcao;

    public $total_etapas = 0;

    /**
     * @inheritdoc
     */
    protected function _preRender()
    {
        parent::_preRender();

        Portabilis_View_Helper_Application::loadStylesheet(
            $this, 'intranet/styles/localizacaoSistema.css'
        );

        $this->breadcrumb(
            'Lista de Chamada Processo Seletivo',
            [
                'selecao_inscritos_lst.php' => 'Candidatos',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function form()
    {
        if (is_null($this->processo_seletivo_id)) {
            $objSelecao = new clsPmieducarProcessoSeletivo();
            $registroSelecao = $objSelecao->getUltimoProcessoSeletivo();
            $this->processo_seletivo_id = $registroSelecao['cod_selecao_processo'];
        }

        $objSelecao = new clsPmieducarProcessoSeletivo(
            $this->processo_seletivo_id
        );

        $registroSelecao = $objSelecao->detalhe();
        $this->total_etapas = $registroSelecao['total_etapas'];
        $this->ano_selecao = $registroSelecao['ref_ano'];

        $this->inputsHelper()->processoSeletivo(
            array('required' => false, 'label' => 'Processo Seletivo')
        );

        $this->inputsHelper()->hidden(
            'total_etapas',
            array('value' => $this->total_etapas)
        );

        $this->inputsHelper()->hidden(
            'ano_selecao',
            array('value' => $this->ano_selecao)
        );

        for ($i = 1; $i <= $this->total_etapas; $i++) {
            $resources = AvaliacaoEtapa::getDescriptiveValues();
            $resources = array_replace([null => $i . 'ª Etapa'], $resources);

            $options = [
                'required' => false,
                'label'    => 'Avaliação Projeto Etapa ' . $i,
                'value'     => $this->{'etapa_' . $i},
                'resources' => $resources,
            ];

            $this->inputsHelper()->select('etapa_' . $i, $options);

            $this->etapas[$i] = $this->{'etapa_' . $i};
        }

        $options = [
            'required' => false,
            'label' => 'Letra inicial do nome entre',
            'placeholder' => '',
            'value' => $this->inicial_min,
            'max_length' => 1,
            'size' => 1,
            'inline' => true
        ];

        $this->inputsHelper()->text('inicial_min', $options);

        $options = [
            'required' => false,
            'label' => ' e',
            'placeholder' => '',
            'value' => $this->inicial_max,
            'max_length' => 1,
            'size' => 1,
            'inline' => false
        ];

        $this->inputsHelper()->text('inicial_max', $options);

        $options = [
            'required' => false,
            'label' => 'Nome Etapa',
            'placeholder' => 'Nome da etapa',
            'value' => $this->nome_etapa,
            'max_length' => 250,
            'size' => 50,
        ];

        $this->inputsHelper()->text('nome_etapa', $options);

        $options = [
            'required' => false,
            'label' => 'Local da etapa',
            'value' => $this->local_etapa,
            'max_length' => 250,
            'size' => 50,
        ];

        $this->inputsHelper()->text('local_etapa', $options);

        $options = [
            'required' => false,
            'label' => 'Data da Etapa',
            'value' => $this->data_etapa,
            'max_length' => 250,
            'size' => 50,
        ];

        $this->inputsHelper()->text('data_etapa', $options);

        $this->inputsHelper()->checkbox(
            'emitir_totalizadores', [
                'label' => 'Adicionar totalizadores ao fim do relatório'
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeValidation()
    {
        $this->report->addArg(
            'processo_seletivo', (int) $this->getRequest()->processo_seletivo_id
        );

        $this->report->addArg(
            'ano_selecao', (int) $this->getRequest()->ano_selecao
        );

        $this->report->addArg(
            'nome_etapa', (String) $this->getRequest()->nome_etapa
        );

        $this->report->addArg(
            'local_etapa', (String) $this->getRequest()->local_etapa
        );

        $this->report->addArg(
            'data_etapa', (String) $this->getRequest()->data_etapa
        );

        for ($i = 1; $i <= $this->total_etapas; $i++) {
            $this->report->addArg(
                'etapa_' . $i, (int) $this->getRequest()->{'etapa_' . $i}
            );
        }

        $this->report->addArg(
            'inicial_min', (string) $this->getRequest()->inicial_min
        );

        $this->report->addArg(
            'inicial_max', (string) $this->getRequest()->inicial_max
        );

        $this->report->addArg(
            'emitir_totalizadores', (bool) $this->getRequest()->emitir_totalizadores
        );
    }

    /**
     * @return SelectiveDocumentReport
     *
     * @throws Exception
     */
    public function report()
    {
        return new SelectiveDocumentReport();
    }
}
