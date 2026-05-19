<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_turma extends CI_Model {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 10 - Turma já cadastrada no sistema.
    // 11 - Turma não encontrada / desativada.

    private function consultarTurma($codigo) {
        $query = $this->db->query("SELECT * FROM tbl_turma WHERE codigo = ?", array($codigo));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function inserir($obj) {
        $turma = $this->consultarTurma($obj->getCodigo());

        if (!is_null($turma)) {
            if ($turma->estatus == 'A') {
                return array('codigo' => 10, 'msg' => 'Turma já cadastrada no sistema.');
            } else {
                return array('codigo' => 10, 'msg' => 'Turma já cadastrada, porém desativada.');
            }
        }

        $dados = array(
            'codigo'     => $obj->getCodigo(),
            'descricao'  => $obj->getDescricao(),
            'capacidade' => $obj->getCapacidade(),
            'dataInicio' => $obj->getDataInicio(),
            'estatus'    => 'A'
        );

        $this->db->insert('tbl_turma', $dados);
        return array('codigo' => 0, 'msg' => 'Turma cadastrada com sucesso.');
    }

    public function consultar($obj) {
        $sql = "SELECT * FROM tbl_turma WHERE estatus = 'A'";
        $params = array();

        if ($obj->getCodigo() != '') {
            $sql .= " AND codigo = ?";
            $params[] = $obj->getCodigo();
        }
        if ($obj->getDescricao() != '') {
            $sql .= " AND descricao LIKE ?";
            $params[] = '%' . $obj->getDescricao() . '%';
        }
        if ($obj->getCapacidade() != '') {
            $sql .= " AND capacidade = ?";
            $params[] = $obj->getCapacidade();
        }
        if ($obj->getDataInicio() != '') {
            $sql .= " AND dataInicio = ?";
            $params[] = $obj->getDataInicio();
        }

        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            return array('codigo' => 0, 'msg' => 'Consulta realizada com sucesso.', 'dados' => $query->result());
        }
        return array('codigo' => 11, 'msg' => 'Nenhuma turma encontrada.', 'dados' => array());
    }

    public function alterar($obj) {
        $turma = $this->consultarTurma($obj->getCodigo());

        if (is_null($turma)) {
            return array('codigo' => 11, 'msg' => 'Turma não encontrada.');
        }
        if ($turma->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Turma desativada.');
        }

        $dados = array();

        if ($obj->getDescricao() != '')  $dados['descricao']  = $obj->getDescricao();
        if ($obj->getCapacidade() != '') $dados['capacidade'] = $obj->getCapacidade();
        if ($obj->getDataInicio() != '') $dados['dataInicio'] = $obj->getDataInicio();

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_turma', $dados);
        return array('codigo' => 0, 'msg' => 'Turma alterada com sucesso.');
    }

    public function desativar($obj) {
        $turma = $this->consultarTurma($obj->getCodigo());

        if (is_null($turma)) {
            return array('codigo' => 11, 'msg' => 'Turma não encontrada.');
        }
        if ($turma->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Turma já desativada.');
        }

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_turma', array('estatus' => 'D'));
        return array('codigo' => 0, 'msg' => 'Turma desativada com sucesso.');
    }
}