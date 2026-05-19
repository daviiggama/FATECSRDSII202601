<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Turma extends CI_Controller {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 1  - Erro nos parâmetros passados.
    // 3  - Conteúdo zerado.
    // 4  - Conteúdo não inteiro.
    // 5  - Conteúdo não é um texto.
    // 6  - Data em formato inválido.
    // 10 - Turma já cadastrada no sistema.
    // 11 - Turma não encontrada / desativada.
    // 12 - Na atualização, pelo menos um atributo deve ser passado.

    private $codigo;
    private $descricao;
    private $capacidade;
    private $dataInicio;

    public function getCodigo()     { return $this->codigo; }
    public function getDescricao()  { return $this->descricao; }
    public function getCapacidade() { return $this->capacidade; }
    public function getDataInicio() { return $this->dataInicio; }

    public function setCodigo($codigo)         { $this->codigo     = $codigo; }
    public function setDescricao($descricao)   { $this->descricao  = $descricao; }
    public function setCapacidade($capacidade) { $this->capacidade = $capacidade; }
    public function setDataInicio($dataInicio) { $this->dataInicio = $dataInicio; }

    public function inserir() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'descricao' => '', 'capacidade' => '', 'dataInicio' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->descricao, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->capacidade, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->dataInicio, 'date');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setCodigo($atributos->codigo);
        $this->setDescricao($atributos->descricao);
        $this->setCapacidade($atributos->capacidade);
        $this->setDataInicio($atributos->dataInicio);

        $this->load->model('M_turma');
        $retorno = $this->M_turma->inserir($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function consultar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'descricao' => '', 'capacidade' => '', 'dataInicio' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->descricao, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->capacidade, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->dataInicio, 'date');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setCodigo($atributos->codigo);
        $this->setDescricao($atributos->descricao);
        $this->setCapacidade($atributos->capacidade);
        $this->setDataInicio($atributos->dataInicio);

        $this->load->model('M_turma');
        $retorno = $this->M_turma->consultar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg'], 'dados' => $retorno['dados']));
    }

    public function alterar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'descricao' => '', 'capacidade' => '', 'dataInicio' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        if ($atributos->descricao == '' && $atributos->capacidade == '' && $atributos->dataInicio == '') {
            echo json_encode(array('codigo' => 12, 'msg' => 'Na atualização, pelo menos um atributo deve ser passado.'));
            return;
        }

        if ($atributos->descricao != '') {
            $retorno = validarDados($atributos->descricao, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->capacidade != '') {
            $retorno = validarDados($atributos->capacidade, 'int');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->dataInicio != '') {
            $retorno = validarDados($atributos->dataInicio, 'date');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        $this->setCodigo($atributos->codigo);
        $this->setDescricao($atributos->descricao);
        $this->setCapacidade($atributos->capacidade);
        $this->setDataInicio($atributos->dataInicio);

        $this->load->model('M_turma');
        $retorno = $this->M_turma->alterar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function desativar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setCodigo($atributos->codigo);

        $this->load->model('M_turma');
        $retorno = $this->M_turma->desativar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }
}