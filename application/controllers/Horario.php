<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horario extends CI_Controller {

    /*
     * Validação dos tipos de retornos nas validações (Código de erro)
     * 1  - Operação realizada no banco com sucesso
     * 2  - Conteúdo passado nulo ou vazio
     * 3  - Conteúdo zerado
     * 4  - Conteúdo não inteiro
     * 5  - Conteúdo não é um texto
     * 6  - Data em formato inválido
     * 7  - Hora em formato inválido
     * 12 - Na atualização, pelo menos um atributo deve ser passado
     * 13 - Hora Final menor que a Hora Inicial
     * 99 - Parâmetros passados do front não correspondem ao método
     */

    // Atributos privados da classe
    private $codigo;
    private $descricao;
    private $horaInicial;
    private $horaFinal;
    private $estatus;
    public function getCodigo() {
        return $this->codigo;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getHoraInicial() {
        return $this->horaInicial;
    }

    public function getHoraFinal() {
        return $this->horaFinal;
    }

    public function getEstatus() {
        return $this->estatus;
    }
    public function setCodigo($codigoFront) {
        $this->codigo = $codigoFront;
    }

    public function setDescricao($descricaoFront) {
        $this->descricao = $descricaoFront;
    }

    public function setHoraInicial($horaInicialFront) {
        $this->horaInicial = $horaInicialFront;
    }

    public function setHoraFinal($horaFinalFront) {
        $this->horaFinal = $horaFinalFront;
    }

    public function setEstatus($estatusFront) {
        $this->estatus = $estatusFront;
    }
    public function inserir() {
        $erros   = [];
        $sucesso = false;

        try {
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {
                $retornoDescricao  = validarDados($resultado->descricao,   'string', true);
                $retornoHoraInicial = validarDados($resultado->horaInicial, 'hora',   true);
                $retornoHoraFinal   = validarDados($resultado->horaFinal,   'hora',   true);
                $retornoComparacao = compararDataHora($resultado->horaInicial,
                                                      $resultado->horaFinal, 'hora');

                if ($retornoDescricao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                'campo'  => 'Descricao',
                                'msg'    => $retornoDescricao['msg']];
                }

                if ($retornoHoraInicial['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoHoraInicial['codigoHelper'],
                                'campo'  => 'Hora Inicial',
                                'msg'    => $retornoHoraInicial['msg']];
                }

                if ($retornoHoraFinal['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoHoraFinal['codigoHelper'],
                                'campo'  => 'Hora Final',
                                'msg'    => $retornoHoraFinal['msg']];
                }
                if ($retornoComparacao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoComparacao['codigoHelper'],
                                'campo'  => 'Hora Inicial e Hora Final',
                                'msg'    => $retornoComparacao['msg']];
                }
                if (empty($erros)) {
                    $this->setDescricao($resultado->descricao);
                    $this->setHoraInicial($resultado->horaInicial);
                    $this->setHoraFinal($resultado->horaFinal);

                    $this->load->model('M_horario');
                    $resBanco = $this->M_horario->inserir(
                        $this->getDescricao(),
                        $this->getHoraInicial(),
                        $this->getHoraFinal()
                    );

                    if ($resBanco['codigo'] == 1) {
                        $sucesso = true;
                    } else {
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg'    => $resBanco['msg']
                        ];
                    }
                }
            }

        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        echo json_encode($retorno);
    }
    public function consultar() {
        $erros   = [];
        $sucesso = false;

        try {

            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo"      => '0',
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {
                $retornoCodigo      = validarDadosConsulta($resultado->codigo,       'int');
                $retornoDescricao   = validarDadosConsulta($resultado->descricao,    'string');
                $retornoHoraInicial = validarDadosConsulta($resultado->horaInicial,  'hora');
                $retornoHoraFinal   = validarDadosConsulta($resultado->horaFinal,    'hora');
                $retornoComparacao = compararDataHora($resultado->horaInicial,
                                                      $resultado->horaFinal, 'hora');

                if ($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo'  => 'Codigo',
                                'msg'    => $retornoCodigo['msg']];
                }

                if ($retornoDescricao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                'campo'  => 'Descricao',
                                'msg'    => $retornoDescricao['msg']];
                }

                if ($retornoHoraInicial['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoHoraInicial['codigoHelper'],
                                'campo'  => 'Hora Inicial',
                                'msg'    => $retornoHoraInicial['msg']];
                }

                if ($retornoHoraFinal['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoHoraFinal['codigoHelper'],
                                'campo'  => 'Hora Final',
                                'msg'    => $retornoHoraFinal['msg']];
                }
                if ($retornoComparacao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoComparacao['codigoHelper'],
                                'campo'  => 'Hora Inicial e Hora Final',
                                'msg'    => $retornoComparacao['msg']];
                }
                if (empty($erros)) {
                    $this->setCodigo($resultado->codigo);
                    $this->setDescricao($resultado->descricao);
                    $this->setHoraInicial($resultado->horaInicial);
                    $this->setHoraFinal($resultado->horaFinal);

                    $this->load->model('M_horario');
                    $resBanco = $this->M_horario->consultar(
                        $this->getCodigo(),
                        $this->getDescricao(),
                        $this->getHoraInicial(),
                        $this->getHoraFinal()
                    );

                    if ($resBanco['codigo'] == 1) {
                        $sucesso = true;
                    } else {
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg'    => $resBanco['msg']
                        ];
                    }
                }
            }

        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg'],
                        'dados'  => $resBanco['dados']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }
        echo json_encode($retorno);
    }
    public function alterar() {
        $erros   = [];
        $sucesso = false;

        try {
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo"      => '0',
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {
                if (trim($resultado->descricao)   == '' &&
                    trim($resultado->horaInicial)  == '' &&
                    trim($resultado->horaFinal)    == '') {
                    $erros[] = ['codigo' => 12, 'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização.'];
                } else {
                    $retornoCodigo      = validarDados($resultado->codigo,          'int',    true);
                    $retornoDescricao   = validarDadosConsulta($resultado->descricao,   'string');
                    $retornoHoraInicial = validarDadosConsulta($resultado->horaInicial, 'hora');
                    $retornoHoraFinal   = validarDadosConsulta($resultado->horaFinal,   'hora');
                    $retornoComparacao = compararDataHora($resultado->horaInicial,
                                                          $resultado->horaFinal, 'hora');

                    if ($retornoCodigo['codigoHelper'] != 0) {
                        $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                    'campo'  => 'Codigo',
                                    'msg'    => $retornoCodigo['msg']];
                    }

                    if ($retornoDescricao['codigoHelper'] != 0) {
                        $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                    'campo'  => 'Descricao',
                                    'msg'    => $retornoDescricao['msg']];
                    }

                    if ($retornoHoraInicial['codigoHelper'] != 0) {
                        $erros[] = ['codigo' => $retornoHoraInicial['codigoHelper'],
                                    'campo'  => 'Hora Inicial',
                                    'msg'    => $retornoHoraInicial['msg']];
                    }

                    if ($retornoHoraFinal['codigoHelper'] != 0) {
                        $erros[] = ['codigo' => $retornoHoraFinal['codigoHelper'],
                                    'campo'  => 'Hora Final',
                                    'msg'    => $retornoHoraFinal['msg']];
                    }

                    // Verifica se a hora inicial é maior que a hora final
                    if ($retornoComparacao['codigoHelper'] != 0) {
                        $erros[] = ['codigo' => $retornoComparacao['codigoHelper'],
                                    'campo'  => 'Hora Inicial e Hora Final',
                                    'msg'    => $retornoComparacao['msg']];
                    }

                    // Se não encontrar erros, manda pro Model
                    if (empty($erros)) {
                        $this->setCodigo($resultado->codigo);
                        $this->setDescricao($resultado->descricao);
                        $this->setHoraInicial($resultado->horaInicial);
                        $this->setHoraFinal($resultado->horaFinal);

                        $this->load->model('M_horario');
                        $resBanco = $this->M_horario->alterar(
                            $this->getCodigo(),
                            $this->getDescricao(),
                            $this->getHoraInicial(),
                            $this->getHoraFinal()
                        );

                        if ($resBanco['codigo'] == 1) {
                            $sucesso = true;
                        } else {
                            $erros[] = [
                                'codigo' => $resBanco['codigo'],
                                'msg'    => $resBanco['msg']
                            ];
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        echo json_encode($retorno);
    }
    public function desativar() {
        $erros   = [];
        $sucesso = false;

        try {

            
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);

            
            $lista = [
                "codigo" => '0'
            ];

            
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {


                $retornoCodigo = validarDados($resultado->codigo, 'int', true);

                if ($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo'  => 'Codigo',
                                'msg'    => $retornoCodigo['msg']];
                }


                if (empty($erros)) {
                    $this->setCodigo($resultado->codigo);

                    $this->load->model('M_horario');
                    $resBanco = $this->M_horario->desativar($this->getCodigo());

                    if ($resBanco['codigo'] == 1) {
                        $sucesso = true;
                    } else {
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg'    => $resBanco['msg']
                        ];
                    }
                }
            }

        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        echo json_encode($retorno);
    }
}


?>