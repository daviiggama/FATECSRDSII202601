<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_usuario extends CI_Model {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 10 - Usuário já cadastrado no sistema.
    // 11 - Usuário não encontrado / desativado.
    // 15 - Login ou senha incorretos.

    private function buscarUsuario($id_usuario) {
        $query = $this->db->query("SELECT * FROM tbl_usuario WHERE id_usuario = ?", array($id_usuario));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    private function buscarUsuarioLogin($usuario) {
        $query = $this->db->query("SELECT * FROM tbl_usuario WHERE usuario = ?", array($usuario));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function inserir($obj) {
        $usuario = $this->buscarUsuarioLogin($obj->getUsuario());

        if (!is_null($usuario)) {
            if ($usuario->estatus == 'A') {
                return array('codigo' => 10, 'msg' => 'Usuário já cadastrado no sistema.');
            } else {
                return array('codigo' => 10, 'msg' => 'Usuário já cadastrado, porém desativado.');
            }
        }

        $dados = array(
            'nome'    => $obj->getNome(),
            'usuario' => $obj->getUsuario(),
            'senha'   => $obj->getSenha(),
            'email'   => $obj->getEmail(),
            'estatus' => 'A'
        );

        $this->db->insert('tbl_usuario', $dados);
        return array('codigo' => 0, 'msg' => 'Usuário cadastrado com sucesso.');
    }

    public function consultar($obj) {
        $sql = "SELECT id_usuario, nome, usuario, email, dtcria, estatus FROM tbl_usuario WHERE estatus = 'A'";
        $params = array();

        if ($obj->getIdUsuario() != '') {
            $sql .= " AND id_usuario = ?";
            $params[] = $obj->getIdUsuario();
        }
        if ($obj->getNome() != '') {
            $sql .= " AND nome LIKE ?";
            $params[] = '%' . $obj->getNome() . '%';
        }
        if ($obj->getUsuario() != '') {
            $sql .= " AND usuario LIKE ?";
            $params[] = '%' . $obj->getUsuario() . '%';
        }
        if ($obj->getEmail() != '') {
            $sql .= " AND email LIKE ?";
            $params[] = '%' . $obj->getEmail() . '%';
        }

        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            return array('codigo' => 0, 'msg' => 'Consulta realizada com sucesso.', 'dados' => $query->result());
        }
        return array('codigo' => 11, 'msg' => 'Nenhum usuário encontrado.', 'dados' => array());
    }

    public function alterar($obj) {
        $usuario = $this->buscarUsuario($obj->getIdUsuario());

        if (is_null($usuario)) {
            return array('codigo' => 11, 'msg' => 'Usuário não encontrado.');
        }
        if ($usuario->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Usuário desativado.');
        }

        $dados = array();

        if ($obj->getNome() != '')    $dados['nome']    = $obj->getNome();
        if ($obj->getUsuario() != '') $dados['usuario'] = $obj->getUsuario();
        if ($obj->getSenha() != '')   $dados['senha']   = $obj->getSenha();
        if ($obj->getEmail() != '')   $dados['email']   = $obj->getEmail();

        $this->db->where('id_usuario', $obj->getIdUsuario());
        $this->db->update('tbl_usuario', $dados);
        return array('codigo' => 0, 'msg' => 'Usuário alterado com sucesso.');
    }

    public function desativar($obj) {
        $usuario = $this->buscarUsuario($obj->getIdUsuario());

        if (is_null($usuario)) {
            return array('codigo' => 11, 'msg' => 'Usuário não encontrado.');
        }
        if ($usuario->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Usuário já desativado.');
        }

        $this->db->where('id_usuario', $obj->getIdUsuario());
        $this->db->update('tbl_usuario', array('estatus' => 'D'));
        return array('codigo' => 0, 'msg' => 'Usuário desativado com sucesso.');
    }

    public function logar($obj) {
        $query = $this->db->query(
            "SELECT * FROM tbl_usuario WHERE usuario = ? AND senha = ? AND estatus = 'A'",
            array($obj->getUsuario(), $obj->getSenha())
        );

        if ($query->num_rows() > 0) {
            return array('codigo' => 0, 'msg' => 'Login realizado com sucesso.');
        }
        return array('codigo' => 15, 'msg' => 'Login ou senha incorretos.');
    }
}