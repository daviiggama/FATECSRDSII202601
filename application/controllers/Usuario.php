<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuario extends CI_Controller {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 1  - Erro nos parâmetros passados.
    // 3  - Conteúdo zerado.
    // 4  - Conteúdo não inteiro.
    // 5  - Conteúdo não é um texto.
    // 8  - E-mail em formato inválido.
    // 10 - Usuário já cadastrado no sistema.
    // 11 - Usuário não encontrado / desativado.
    // 12 - Na atualização, pelo menos um atributo deve ser passado.
    // 15 - Login ou senha incorretos.

    private $id_usuario;
    private $nome;
    private $usuario;
    private $senha;
    private $email;

    public function getIdUsuario() { return $this->id_usuario; }
    public function getNome()      { return $this->nome; }
    public function getUsuario()   { return $this->usuario; }
    public function getSenha()     { return $this->senha; }
    public function getEmail()     { return $this->email; }

    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function setNome($nome)            { $this->nome       = $nome; }
    public function setUsuario($usuario)      { $this->usuario    = $usuario; }
    public function setSenha($senha)          { $this->senha      = $senha; }
    public function setEmail($email)          { $this->email      = $email; }

    public function inserir() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('nome' => '', 'usuario' => '', 'senha' => '', 'email' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->nome, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->usuario, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->senha, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->email, 'email');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setNome($atributos->nome);
        $this->setUsuario($atributos->usuario);
        $this->setSenha(md5($atributos->senha));
        $this->setEmail($atributos->email);

        $this->load->model('M_usuario');
        $retorno = $this->M_usuario->inserir($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function consultar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('id_usuario' => '', 'nome' => '', 'usuario' => '', 'email' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDadosConsulta($atributos->id_usuario, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->nome, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->usuario, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDadosConsulta($atributos->email, 'email');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setIdUsuario($atributos->id_usuario);
        $this->setNome($atributos->nome);
        $this->setUsuario($atributos->usuario);
        $this->setEmail($atributos->email);

        $this->load->model('M_usuario');
        $retorno = $this->M_usuario->consultar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg'], 'dados' => $retorno['dados']));
    }

    public function alterar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('id_usuario' => '', 'nome' => '', 'usuario' => '', 'senha' => '', 'email' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->id_usuario, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        if ($atributos->nome == '' && $atributos->usuario == '' && $atributos->senha == '' && $atributos->email == '') {
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

        if ($atributos->usuario != '') {
            $retorno = validarDados($atributos->usuario, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->senha != '') {
            $retorno = validarDados($atributos->senha, 'string');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        if ($atributos->email != '') {
            $retorno = validarDados($atributos->email, 'email');
            if ($retorno['codigoHelper'] != 0) {
                echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
                return;
            }
        }

        $this->setIdUsuario($atributos->id_usuario);
        $this->setNome($atributos->nome);
        $this->setUsuario($atributos->usuario);
        $this->setSenha($atributos->senha != '' ? md5($atributos->senha) : '');
        $this->setEmail($atributos->email);

        $this->load->model('M_usuario');
        $retorno = $this->M_usuario->alterar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function desativar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('id_usuario' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->id_usuario, 'int');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setIdUsuario($atributos->id_usuario);

        $this->load->model('M_usuario');
        $retorno = $this->M_usuario->desativar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }

    public function logar() {
        $atributos = json_decode(file_get_contents('php://input'));
        $lista = array('usuario' => '', 'senha' => '');

        if (verificarParam($atributos, $lista) == 0) {
            echo json_encode(array('codigo' => 1, 'msg' => 'Erro nos parâmetros passados.'));
            return;
        }

        $retorno = validarDados($atributos->usuario, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $retorno = validarDados($atributos->senha, 'string');
        if ($retorno['codigoHelper'] != 0) {
            echo json_encode(array('codigo' => $retorno['codigoHelper'], 'msg' => $retorno['msg']));
            return;
        }

        $this->setUsuario($atributos->usuario);
        $this->setSenha(md5($atributos->senha));

        $this->load->model('M_usuario');
        $retorno = $this->M_usuario->logar($this);

        echo json_encode(array('codigo' => $retorno['codigo'], 'msg' => $retorno['msg']));
    }
}