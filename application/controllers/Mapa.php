<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mapa extends CI_Controller {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 1  - Erro nos parâmetros passados.
    // 3  - Conteúdo zerado.
    // 4  - Conteúdo não inteiro.
    // 6  - Data em formato inválido.
    // 10 - Mapa já cadastrado no sistema.
    // 11 - Mapa não encontrado / desativado.
    // 12 - Na atualização, pelo menos um atributo deve ser passado.

    private $codigo;
    private $datareserva;
    private $sala;
    private $codigo_horario;
    private $codigo_turma;
    private $codigo_professor;

    public function getCodigo()          { return $this->codigo; }
    public function getDatareserva()     { return $this->datareserva; }
    public function getSala()            { return $this->sala; }
    public function getCodigoHorario()   { return $this->codigo_horario; }
    public function getCodigoTurma()     { return $this->codigo_turma; }
    public function getCodigoProfessor() { return $this->codigo_professor; }

    public function setCodigo($codigo)                   { $this->codigo           = $codigo; }
    public function setDatareserva($datareserva)         { $this->datareserva      = $datareserva; }
    public function setSala($sala)                       { $this->sala             = $sala; }
    public function setCodigoHorario($codigo_horario)   { $this->codigo_horario   = $codigo_horario; }
    public function setCodigoTurma($codigo_turma)       { $this->codigo_turma     = $codigo_turma; }
    public function setCodigoProfessor($codigo_professor){ $this->codigo_professor = $codigo_professor; }

    public function inserir() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('datareserva' => '', 'sala' => '', 'codigo_horario' => '', 'codigo_turma' => '', 'codigo_professor' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->datareserva, 'date');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->sala, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->codigo_horario, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->codigo_turma, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->codigo_professor, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setDatareserva($atributos->datareserva);
        $this->setSala($atributos->sala);
        $this->setCodigoHorario($atributos->codigo_horario);
        $this->setCodigoTurma($atributos->codigo_turma);
        $this->setCodigoProfessor($atributos->codigo_professor);

        $this->load->model('M_mapa');
        $retorno = $this->M_mapa->inserir($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function consultar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'datareserva' => '', 'sala' => '', 'codigo_horario' => '', 'codigo_turma' => '', 'codigo_professor' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->datareserva, 'date');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->sala, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo_horario, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo_turma, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->codigo_professor, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setCodigo($atributos->codigo);
        $this->setDatareserva($atributos->datareserva);
        $this->setSala($atributos->sala);
        $this->setCodigoHorario($atributos->codigo_horario);
        $this->setCodigoTurma($atributos->codigo_turma);
        $this->setCodigoProfessor($atributos->codigo_professor);

        $this->load->model('M_mapa');
        $retorno = $this->M_mapa->consultar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg'], 'dados' => $retorno['dados']));
    }

    public function alterar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('codigo' => '', 'datareserva' => '', 'sala' => '', 'codigo_horario' => '', 'codigo_turma' => '', 'codigo_professor' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->codigo, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        if ($atributos->datareserva == '' && $atributos->sala == '' && $atributos->codigo_horario == '' && $atributos->codigo_turma == '' && $atributos->codigo_professor == '') {
            echo json_encode(array('codigo' => 12, 'msg' => 'Na atualização, pelo menos um atributo deve ser passado.'));
            return;
        }

        if ($atributos->datareserva != '') {
            $retorno = validarDados($atributos->datareserva, 'date');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->sala != '') {
            $retorno = validarDados($atributos->sala, 'int');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->codigo_horario != '') {
            $retorno = validarDados($atributos->codigo_horario, 'int');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->codigo_turma != '') {
            $retorno = validarDados($atributos->codigo_turma, 'int');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->codigo_professor != '') {
            $retorno = validarDados($atributos->codigo_professor, 'int');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        $this->setCodigo($atributos->codigo);
        $this->setDatareserva($atributos->datareserva);
        $this->setSala($atributos->sala);
        $this->setCodigoHorario($atributos->codigo_horario);
        $this->setCodigoTurma($atributos->codigo_turma);
        $this->setCodigoProfessor($atributos->codigo_professor);

        $this->load->model('M_mapa');
        $retorno = $this->M_mapa->alterar($this);

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

        $this->load->model('M_mapa');
        $retorno = $this->M_mapa->desativar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }
}