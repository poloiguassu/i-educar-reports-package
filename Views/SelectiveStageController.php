<?php

use iEducar\Modules\Inscritos\Model\AvaliacaoEtapa;

require_once 'lib/Portabilis/Controller/ReportCoreController.php';
require_once 'Reports/Reports/SelectiveStageReport.php';

class SelectiveStageController extends Portabilis_Controller_ReportCoreController
{
    /**
     * @var int
     */
    protected $_processoAp = 21473;

    /**
     * @var string
     */
    protected $_titulo = 'Lista de selecionados Processo Seletivo';

    /**
     * @var int
     */
    public $periodo;

    /**
     * @var int
     */
    public $cod_servidor_funcao;

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
        $this->ano_selecao = $registroSelecao['ref_ano'];

        $this->inputsHelper()->processoSeletivo(
            array('required' => true, 'label' => 'Processo Seletivo')
        );

        $this->inputsHelper()->hidden(
            'ano_selecao',
            array('value' => $this->ano_selecao)
        );

        $options = [
            'required' => true,
            'label'    => 'Etapa',
            'value'     => $this->etapa,
            'resources' => array(1 => 'Etapa 1', 2 => 'Etapa 2'),
        ];

        $this->inputsHelper()->select('etapa', $options);

        $resources = AvaliacaoEtapa::getDescriptiveValues();
        $resources = array_replace([null => 'Situação Etapa'], $resources);

        $options = [
            'required' => false,
            'label'    => 'Situação da Etapa ',
            'value'     => $this->etapa_situacao,
            'resources' => $resources,
        ];

        $this->inputsHelper()->select('etapa_situacao', $options);

        $this->inputsHelper()->selecaoDataEtapa(
            array(
                'required' => false,
                'label' => 'Data da Etapa',
                'value' => $this->selecao_data_etapa_id
            )
        );

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
            'local_etapa', (String) Portabilis_String_Utils::toLatin1($this->getRequest()->local_etapa)
        );

        $this->report->addArg(
            'data_etapa', (String) Portabilis_String_Utils::toLatin1($this->getRequest()->data_etapa)
        );

        $this->report->addArg(
            'etapa', (int) $this->getRequest()->etapa
        );

        $this->report->addArg(
            'etapa_situacao', (int) $this->getRequest()->etapa_situacao
        );

        $this->report->addArg(
            'etapa_data', (int) $this->getRequest()->selecao_data_etapa_id
        );

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
        return new SelectiveStageReport();
    }
}
