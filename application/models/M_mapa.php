<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_mapa extends CI_Model {

    // Códigos de retorno:
    // 0  - Operação realizada com sucesso.
    // 10 - Mapa já cadastrado no sistema.
    // 11 - Mapa não encontrado / desativado.

    private function consultarMapa($codigo) {
        $query = $this->db->query("SELECT * FROM tbl_mapa WHERE codigo = ?", array($codigo));
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    private function verificarConflito($sala, $codigo_horario, $datareserva, $codigo_ignorar = null) {
        $sql = "SELECT * FROM tbl_mapa WHERE sala = ? AND codigo_horario = ? AND datareserva = ? AND estatus = 'A'";
        $params = array($sala, $codigo_horario, $datareserva);

        if (!is_null($codigo_ignorar)) {
            $sql .= " AND codigo != ?";
            $params[] = $codigo_ignorar;
        }

        $query = $this->db->query($sql, $params);
        return $query->num_rows() > 0;
    }

    public function inserir($obj) {
        if ($this->verificarConflito($obj->getSala(), $obj->getCodigoHorario(), $obj->getDatareserva())) {
            return array('codigo' => 10, 'msg' => 'Já existe uma reserva para esta sala neste horário e data.');
        }

        $dados = array(
            'datareserva'      => $obj->getDatareserva(),
            'sala'             => $obj->getSala(),
            'codigo_horario'   => $obj->getCodigoHorario(),
            'codigo_turma'     => $obj->getCodigoTurma(),
            'codigo_professor' => $obj->getCodigoProfessor(),
            'estatus'          => 'A'
        );

        $this->db->insert('tbl_mapa', $dados);
        return array('codigo' => 0, 'msg' => 'Reserva cadastrada com sucesso.');
    }

    public function consultar($obj) {
        $sql = "SELECT * FROM tbl_mapa WHERE estatus = 'A'";
        $params = array();

        if ($obj->getCodigo() != '') {
            $sql .= " AND codigo = ?";
            $params[] = $obj->getCodigo();
        }
        if ($obj->getDatareserva() != '') {
            $sql .= " AND datareserva = ?";
            $params[] = $obj->getDatareserva();
        }
        if ($obj->getSala() != '') {
            $sql .= " AND sala = ?";
            $params[] = $obj->getSala();
        }
        if ($obj->getCodigoHorario() != '') {
            $sql .= " AND codigo_horario = ?";
            $params[] = $obj->getCodigoHorario();
        }
        if ($obj->getCodigoTurma() != '') {
            $sql .= " AND codigo_turma = ?";
            $params[] = $obj->getCodigoTurma();
        }
        if ($obj->getCodigoProfessor() != '') {
            $sql .= " AND codigo_professor = ?";
            $params[] = $obj->getCodigoProfessor();
        }

        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            return array('codigo' => 0, 'msg' => 'Consulta realizada com sucesso.', 'dados' => $query->result());
        }
        return array('codigo' => 11, 'msg' => 'Nenhuma reserva encontrada.', 'dados' => array());
    }

    public function alterar($obj) {
        $mapa = $this->consultarMapa($obj->getCodigo());

        if (is_null($mapa)) {
            return array('codigo' => 11, 'msg' => 'Reserva não encontrada.');
        }
        if ($mapa->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Reserva desativada.');
        }

        $dados = array();

        if ($obj->getDatareserva() != '')     $dados['datareserva']      = $obj->getDatareserva();
        if ($obj->getSala() != '')            $dados['sala']             = $obj->getSala();
        if ($obj->getCodigoHorario() != '')   $dados['codigo_horario']   = $obj->getCodigoHorario();
        if ($obj->getCodigoTurma() != '')     $dados['codigo_turma']     = $obj->getCodigoTurma();
        if ($obj->getCodigoProfessor() != '') $dados['codigo_professor'] = $obj->getCodigoProfessor();

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_mapa', $dados);
        return array('codigo' => 0, 'msg' => 'Reserva alterada com sucesso.');
    }

    public function desativar($obj) {
        $mapa = $this->consultarMapa($obj->getCodigo());

        if (is_null($mapa)) {
            return array('codigo' => 11, 'msg' => 'Reserva não encontrada.');
        }
        if ($mapa->estatus != 'A') {
            return array('codigo' => 11, 'msg' => 'Reserva já desativada.');
        }

        $this->db->where('codigo', $obj->getCodigo());
        $this->db->update('tbl_mapa', array('estatus' => 'D'));
        return array('codigo' => 0, 'msg' => 'Reserva desativada com sucesso.');
    }
}