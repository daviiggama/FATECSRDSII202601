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

            // Verifica se a sala já está cadastrada
            $retornoConsulta = $this->consultarSala($codigo);

            if ($retornoConsulta['codigo'] != 9 &&
                $retornoConsulta['codigo'] != 10) {

                // Query de inserção
                $this->db->query("insert into tbl_sala (codigo, descricao, andar, capacidade)
                                  values ($codigo, '$descricao', $andar, $capacidade)");

                // Verifica se a inserção ocorreu com sucesso
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

    // Método privado — só usado dentro desta classe
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

            // Query base — traz só salas ativas
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

            // Verifica se encontrou registros
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

            // Antes de alterar, verifica se a sala existe no banco
            $retornoConsulta = $this->consultarSala($codigo);

            // Só altera se a sala estiver cadastrada (codigo 10 = sala existe e está ativa)
            if ($retornoConsulta['codigo'] == 10) {

                // Inicia a query de atualização
                $query = "update tbl_sala set ";

                // Adiciona os campos dinamicamente, só se foram passados
                if ($descricao !== '') {
                    $query .= "descricao = '$descricao', ";
                }

                if ($andar !== '') {
                    $query .= "andar = $andar, ";
                }

                if ($capacidade !== '') {
                    $query .= "capacidade = $capacidade, ";
                }

                // Remove a vírgula do final e adiciona o where
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                // atualiza
                $this->db->query($queryFinal);

                // verifica se atualizou
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
                // Retorna o erro que veio do consultarSala (9 = desativada, 98 = não encontrada)
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
                );
            }

        } catch (Exception $e) {
            // Captura qualquer erro inesperado
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        // Retorna o resultado pro Controller
        return $dados;
    }

    public function desativar($codigo) {
        try {

            // Antes de desativar, verifica se a sala existe no banco
            $retornoConsulta = $this->consultarSala($codigo);

            // Só desativa se a sala estiver cadastrada e ativa (codigo 10 = sala existe)
            if ($retornoConsulta['codigo'] == 10) {

                // Atualiza o estatus para 'D' (desativado) — não apaga o registro
                $this->db->query("update tbl_sala set estatus = 'D'
                                  where codigo = $codigo");

                // Verifica se a atualização ocorreu com sucesso
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
                // Retorna o erro que veio do consultarSala (9 = já desativada, 98 = não encontrada)
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
                );
            }

        } catch (Exception $e) {
            // Captura qualquer erro inesperado
            $dados = array(
                'codigo' => 0,
                'msg'    => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        // Retorna o resultado pro Controller
        return $dados;
    }
}
?>