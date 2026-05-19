<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_professor extends CI_Model {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 10 - Professor já cadastrado no sistema.
    // 11 - Professor não encontrado / desativado.

    private function consultarProfessor($codigo) {
        $query = $this->db->query("SELECT * FROM tbl_professor WHERE codigo = ?", array($codigo));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    private function consultarProfessorCpf($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $query = $this->db->query("SELECT * FROM tbl_professor WHERE cpf = ?", array($cpf));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function inserir($obj) {
        $cpfLimpo = preg_replace('/[^0-9]/', '', $obj->getCpf());
        $professor = $this->consultarProfessorCpf($cpfLimpo);

        if (!is_null($professor)) {
            if ($professor->estatus == 'A') {
                return array('codigo' => 10, 'msg' => 'Professor já cadastrado no sistema.');
            } else {
                return array('codigo' => 10, 'msg' => 'Professor já cadastrado, porém desativado.');
            }
        }

        $dados = array(
            'nome'    => $obj->getNome(),
            'cpf'     => $cpfLimpo,
            'tipo'    => $obj->getTipo(),
            'estatus' => 'A'
        );

        $this->db->insert('tbl_professor', $dados);
        return array('codigo' => 0, 'msg' => 'Professor cadastrado com sucesso.');
    }

    public function consultar($obj) {
        $sql = "SELECT * FROM tbl_professor WHERE estatus = 'A'";
        $params = array();

        if ($obj->getCodigo() != '') {
            $sql .= " AND codigo = ?";
            $params[] = $obj->getCodigo();
        }
        if ($obj->getNome() != '') {
            $sql .= " AND nome LIKE ?";
            $params[] = '%' . $obj->getNome() . '%';
        }
        if ($obj->getCpf() != '') {
            $sql .= " AND cpf = ?";
            $params[] = preg_replace('/[^0-9]/', '', $obj->getCpf());
        }
        if ($obj->getTipo() != '') {
            $sql .= " AND tipo = ?";
            $params[] = $obj->getTipo();
        }

        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            return array('codigo' => 0, 'msg' => 'Consulta realizada com sucesso.', 'dados' => $query->result());
        }
        return array('codigo' => 11, 'msg' => 'Nenhum professor encontrado.', 'dados' => array());
    }

    public function alterar($obj) {
        $professor = $this->consultarProfessor($obj->getCodigo());

        if (is_null($professor)) {
            return array('codigo' => 11, 'msg' => 'Professor não encontrado.');
        }
        if ($professor->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Professor desativado.');
        }

        $dados = array();

        if ($obj->getNome() != '') $dados['nome'] = $obj->getNome();
        if ($obj->getCpf() != '')  $dados['cpf']  = preg_replace('/[^0-9]/', '', $obj->getCpf());
        if ($obj->getTipo() != '') $dados['tipo']  = $obj->getTipo();

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_professor', $dados);
        return array('codigo' => 0, 'msg' => 'Professor alterado com sucesso.');
    }

    public function desativar($obj) {
        $professor = $this->consultarProfessor($obj->getCodigo());

        if (is_null($professor)) {
            return array('codigo' => 11, 'msg' => 'Professor não encontrado.');
        }
        if ($professor->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Professor já desativado.');
        }

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_professor', array('estatus' => 'D'));
        return array('codigo' => 0, 'msg' => 'Professor desativado com sucesso.');
    }
}