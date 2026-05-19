<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Professor extends CI_Controller {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 1  - Erro nos parâmetros passados.
    // 3  - Conteúdo zerado.
    // 4  - Conteúdo não inteiro.
    // 5  - Conteúdo não é um texto.
    // 9  - CPF inválido.
    // 10 - Professor já cadastrado no sistema.
    // 11 - Professor não encontrado / desativado.
    // 12 - Na atualização, pelo menos um atributo deve ser passado.

    private $codigo;
    private $nome;
    private $cpf;
    private $tipo;

    public function getCodigo() { return $this->codigo; }
    public function getNome()   { return $this->nome; }
    public function getCpf()    { return $this->cpf; }
    public function getTipo()   { return $this->tipo; }

    public function setCodigo($codigo) { $this->codigo = $codigo; }
    public function setNome($nome)     { $this->nome   = $nome; }
    public function setCpf($cpf)       { $this->cpf    = $cpf; }
    public function setTipo($tipo)     { $this->tipo   = $tipo; }

    public function inserir() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('nome' => '', 'cpf' => '', 'tipo' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->nome, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->cpf, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarCPF($atributos->cpf);
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->tipo, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setNome($atributos->nome);
        $this->setCpf($atributos->cpf);
        $this->setTipo($atributos->tipo);

        $this->load->model('M_professor');
        $retorno = $this->M_professor->inserir($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function consultar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'nome' => '', 'cpf' => '', 'tipo' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->nome, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->cpf, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->tipo, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setCodigo($atributos->codigo);
        $this->setNome($atributos->nome);
        $this->setCpf($atributos->cpf);
        $this->setTipo($atributos->tipo);

        $this->load->model('M_professor');
        $retorno = $this->M_professor->consultar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg'], 'dados' => $retorno['dados']));
    }

    public function alterar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'nome' => '', 'cpf' => '', 'tipo' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        if ($atributos->nome == '' && $atributos->cpf == '' && $atributos->tipo == '') {
            echo json_encode(array('codigo' => 12, 'msg' => 'Na atualização, pelo menos um atributo deve ser passado.'));
            return;
        }

        if ($atributos->nome != '') {
            $retorno = validarDados($atributos->nome, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->cpf != '') {
            $retorno = validarDados($atributos->cpf, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
            $retorno = validarCPF($atributos->cpf);
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->tipo != '') {
            $retorno = validarDados($atributos->tipo, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        $this->setCodigo($atributos->codigo);
        $this->setNome($atributos->nome);
        $this->setCpf($atributos->cpf);
        $this->setTipo($atributos->tipo);

        $this->load->model('M_professor');
        $retorno = $this->M_professor->alterar($this);

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

        $this->load->model('M_professor');
        $retorno = $this->M_professor->desativar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }
}