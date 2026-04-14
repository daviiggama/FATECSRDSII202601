<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_horario extends CI_Model {

    /*
     * Validação dos tipos de retornos (Código de erro)
     * 0  - Erro de exceção
     * 1  - Operação realizada no banco com sucesso
     * 8  - Houve algum problema de inserção, atualização, consulta ou exclusão
     * 9  - Horário desativado no sistema
     * 10 - Horário já cadastrado
     * 11 - Horário não encontrado
     * 98 - Método auxiliar de consulta que não trouxe dados
     */

    public function inserir($descricao, $horaInicial, $horaFinal) {
        try {

            // Verifica se o horário já está cadastrado antes de inserir
            $retornoConsulta = $this->consultarHorario('', $horaInicial, $horaFinal);

            // Só insere se o horário não existir (98 = não encontrado)
            if ($retornoConsulta['codigo'] != 9 &&
                $retornoConsulta['codigo'] != 10) {

                // Query de inserção
                $this->db->query("insert into tbl_horario (descricao, hora_ini, hora_fim)
                                  values ('$descricao', '$horaInicial', '$horaFinal')");

                // Verifica se a inserção ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Horário cadastrado corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na inserção na tabela de horários.'
                    );
                }

            } else {
                // Retorna o erro que veio do consultarHorario
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

    // Método privado — só usado internamente para verificar se horário existe
    private function consultarHorario($codigo, $horaInicial, $horaFinal) {
        try {

            // Monta a query de acordo com o que foi passado
            if ($codigo != '') {
                // Busca pelo codigo
                $sql = "select * from tbl_horario where codigo = $codigo";
            } else {
                // Busca pela hora inicial e final
                $sql = "select * from tbl_horario
                        where hora_ini = '$horaInicial'
                        and hora_fim = '$horaFinal'";
            }

            $retornoHorario = $this->db->query($sql);

            if ($retornoHorario->num_rows() > 0) {
                $linha = $retornoHorario->row();

                // Verifica se o horário está desativado
                if (trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 9,
                        'msg'    => 'Horário desativado no sistema, caso precise reativar o mesmo, fale com o administrador.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 10,
                        'msg'    => 'Horário já cadastrado no sistema.'
                    );
                }
            } else {
                $dados = array(
                    'codigo' => 98,
                    'msg'    => 'Horário não encontrado.'
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

    public function consultar($codigo, $descricao, $horaInicial, $horaFinal) {
        try {

            // Query base — traz só horários ativos
            $sql = "select * from tbl_horario where estatus = '' ";

            // Adiciona filtros dinamicamente conforme o que foi passado
            if (trim($codigo) != '') {
                $sql = $sql . " and codigo = $codigo ";
            }

            if (trim($descricao) != '') {
                $sql = $sql . " and descricao like '%$descricao%' ";
            }

            if (trim($horaInicial) != '') {
                $sql = $sql . " and hora_ini = '$horaInicial' ";
            }

            if (trim($horaFinal) != '') {
                $sql = $sql . " and hora_fim = '$horaFinal' ";
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
                    'msg'    => 'Horário não encontrado.'
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

    public function alterar($codigo, $descricao, $horaInicial, $horaFinal) {
        try {

            // Verifica se o horário existe antes de alterar
            $retornoConsulta = $this->consultarHorario($codigo, '', '');

            // Só altera se o horário estiver cadastrado e ativo (codigo 10 = existe)
            if ($retornoConsulta['codigo'] == 10) {

                // Inicia a query de atualização
                $query = "update tbl_horario set ";

                // Adiciona os campos dinamicamente, só se foram passados
                if ($descricao !== '') {
                    $query .= "descricao = '$descricao', ";
                }

                if ($horaInicial !== '') {
                    $query .= "hora_ini = '$horaInicial', ";
                }

                if ($horaFinal !== '') {
                    $query .= "hora_fim = '$horaFinal', ";
                }

                // Remove a vírgula do final e adiciona o where
                $queryFinal = rtrim($query, ", ") . " where codigo = $codigo";

                // Executa a query de atualização
                $this->db->query($queryFinal);

                // Verifica se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Horário atualizado corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na atualização na tabela de horário.'
                    );
                }

            } else {
                // Retorna o erro que veio do consultarHorario
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
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

    public function desativar($codigo) {
        try {

            // Verifica se o horário existe antes de desativar
            $retornoConsulta = $this->consultarHorario($codigo, '', '');

            // Só desativa se o horário estiver cadastrado e ativo (codigo 10 = existe)
            if ($retornoConsulta['codigo'] == 10) {

                // Atualiza o estatus para 'D' (desativado) — não apaga o registro
                $this->db->query("update tbl_horario set estatus = 'D'
                                  where codigo = $codigo");

                // Verifica se a atualização ocorreu com sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg'    => 'Horário DESATIVADO corretamente.'
                    );
                } else {
                    $dados = array(
                        'codigo' => 8,
                        'msg'    => 'Houve algum problema na DESATIVAÇÃO do Horário.'
                    );
                }

            } else {
                // Retorna o erro que veio do consultarHorario
                $dados = array(
                    'codigo' => $retornoConsulta['codigo'],
                    'msg'    => $retornoConsulta['msg']
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

}
?>