<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_sala extends CI_Model {

    /*
     * Validação dos tipos de retornos (Código de erro)
     * 0  - Erro de exceção
     * 1  - Operação realizada no banco com sucesso
     * 8  - Houve algum problema de inserção, atualização, consulta ou exclusão
     * 9  - Sala desativada no sistema
     * 10 - Sala já cadastrada
     * 11 - Sala não encontrada
     * 98 - Método auxiliar de consulta que não trouxe dados
     */

    public function inserir($codigo, $descricao, $andar, $capacidade) {
        try {
            $retornoConsulta = $this->consultarSala($codigo);

            if ($retornoConsulta['codigo'] != 9 &&
                $retornoConsulta['codigo'] != 10) {
                $this->db->query("insert into tbl_sala (codigo, descricao, andar, capacidade)
                                  values ($codigo, '$descricao', $andar, $capacidade)");
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Sala cadastrada corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na inserção na tabela de salas.'
                    );
                }

            } else {
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
                );
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        return $dados;
    }
    private function consultarSala($codigo) {
        try {

            $sql = "select * from tbl_sala where codigo = $codigo";
            $retornoSala = $this->db->query($sql);

            if ($retornoSala->num_rows() > 0) {
                $linha = $retornoSala->row();

                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 9,
                        'msg'    => 'Sala desativada no sistema, caso precise reativar a mesma, fale com o administrador.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 10,
                        'msg'    => 'Sala já cadastrada no sistema.'
                    );
                }
            } else {
                $dados = array(
                    'codigo' => 98,
                    'msg'    => 'Sala não encontrada.'
                );
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        return $dados;
    }

    public function consultar($codigo, $descricao, $andar, $capacidade) {
        try {
            $sql = "select * from tbl_sala where estatus = '' ";

            // Adiciona filtros dinamicamente conforme o que foi passado
            if (trim($codigo) != '') {
                $sql = $sql . " and codigo = $codigo ";
            }

            if (trim($andar) != '') {
                $sql = $sql . " and andar = '$andar' ";
            }

            if (trim($descricao) != '') {
                $sql = $sql . " and descricao like '%$descricao%' ";
            }

            if (trim($capacidade) != '') {
                $sql = $sql . " and capacidade = '$capacidade' ";
            }

            $sql = $sql . " order by codigo ";

            $retorno = $this->db->query($sql);
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg'    => 'Consulta efetuada com sucesso.',
                    'dados'  => $retorno->result()
                );
            } else {
                $dados = array(
                    'codigo' => 11,
                    'msg'    => 'Sala não encontrada.'
                );
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        return $dados;
    }

    public function alterar($codigo, $descricao, $andar, $capacidade) {
        try {

            $retornoConsulta = $this->consultarSala($codigo);
            if ($retornoConsulta['codigo'] == 10) {
                $query = "update tbl_sala set ";
                if ($descricao !== '') {
                    $query .= "descricao = '$descricao', ";
                }

                if ($andar !== '') {
                    $query .= "andar = $andar, ";
                }

                if ($capacidade !== '') {
                    $query .= "capacidade = $capacidade, ";
                }
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";
                $this->db->query($queryFinal);
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Sala atualizada corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na atualização na tabela de salas.'
                    );
                }

            } else {
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
                );
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        return $dados;
    }

    public function desativar($codigo) {
        try {
            $retornoConsulta = $this->consultarSala($codigo);
            if ($retornoConsulta['codigo'] == 10) {
                $this->db->query("update tbl_sala set estatus = 'D'
                                  where codigo = $codigo");
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Sala DESATIVADA corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na DESATIVAÇÃO da Sala.'
                    );
                }

            } else {
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
                );
            }

        } catch (Exception $e) {
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        return $dados;
    }
}
?>