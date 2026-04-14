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

    // Getters dos atributos
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

    // Setters dos atributos
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
        // Atributos para controlar o status do método
        $erros   = [];
        $sucesso = false;

        try {

            // Lê o JSON enviado pelo Insomnia
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Campos que esse método espera receber
            $lista = [
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];

            // Verifica se os campos do Front batem com os esperados
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {

                // Valida cada campo individualmente
                $retornoDescricao  = validarDados($resultado->descricao,   'string', true);
                $retornoHoraInicial = validarDados($resultado->horaInicial, 'hora',   true);
                $retornoHoraFinal   = validarDados($resultado->horaFinal,   'hora',   true);

                // Verifica se a hora inicial é maior que a hora final
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

                // Verifica se a hora inicial é maior que a hora final
                if ($retornoComparacao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoComparacao['codigoHelper'],
                                'campo'  => 'Hora Inicial e Hora Final',
                                'msg'    => $retornoComparacao['msg']];
                }

                // Se não encontrar erros, manda pro Model
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

        // Monta o retorno
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        // Transforma em JSON e retorna
        echo json_encode($retorno);
    }
    public function consultar() {
        // Atributos para controlar o status do método
        $erros   = [];
        $sucesso = false;

        try {

            // Lê o JSON enviado pelo Insomnia
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Campos que esse método espera receber
            $lista = [
                "codigo"      => '0',
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];

            // Verifica se os campos do Front batem com os esperados
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {

                // Valida cada campo — consulta aceita campos vazios
                $retornoCodigo      = validarDadosConsulta($resultado->codigo,       'int');
                $retornoDescricao   = validarDadosConsulta($resultado->descricao,    'string');
                $retornoHoraInicial = validarDadosConsulta($resultado->horaInicial,  'hora');
                $retornoHoraFinal   = validarDadosConsulta($resultado->horaFinal,    'hora');

                // Verifica se a hora inicial é maior que a hora final
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

        // Monta o retorno
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg'],
                        'dados'  => $resBanco['dados']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        // Transforma em JSON e retorna
        echo json_encode($retorno);
    }
    public function alterar() {
        // Atributos para controlar o status do método
        $erros   = [];
        $sucesso = false;

        try {

            // Lê o JSON enviado pelo Insomnia
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Campos que esse método espera receber
            $lista = [
                "codigo"      => '0',
                "descricao"   => '0',
                "horaInicial" => '0',
                "horaFinal"   => '0'
            ];

            // Verifica se os campos do Front batem com os esperados
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {

                // Pelo menos um dos três parâmetros precisa ter dados para atualizar
                if (trim($resultado->descricao)   == '' &&
                    trim($resultado->horaInicial)  == '' &&
                    trim($resultado->horaFinal)    == '') {
                    $erros[] = ['codigo' => 12, 'msg' => 'Pelo menos um parâmetro precisa ser passado para atualização.'];
                } else {

                    // Codigo é obrigatório, os demais são opcionais
                    $retornoCodigo      = validarDados($resultado->codigo,          'int',    true);
                    $retornoDescricao   = validarDadosConsulta($resultado->descricao,   'string');
                    $retornoHoraInicial = validarDadosConsulta($resultado->horaInicial, 'hora');
                    $retornoHoraFinal   = validarDadosConsulta($resultado->horaFinal,   'hora');

                    // Verifica se a hora inicial é maior que a hora final
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

        // Monta o retorno
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        // Transforma em JSON e retorna
        echo json_encode($retorno);
    }
    public function desativar() {
        // Atributos para controlar o status do método
        $erros   = [];
        $sucesso = false;

        try {

            // Lê o JSON enviado pelo Insomnia
            $json      = file_get_contents('php://input');
            $resultado = json_decode($json);

            // Só precisa do codigo para desativar
            $lista = [
                "codigo" => '0'
            ];

            // Verifica se os campos do Front batem com os esperados
            if (verificarParam($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {

                // Valida o codigo — único campo obrigatório
                $retornoCodigo = validarDados($resultado->codigo, 'int', true);

                if ($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo'  => 'Codigo',
                                'msg'    => $retornoCodigo['msg']];
                }

                // Se não encontrar erros, manda pro Model
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

        // Monta o retorno
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg'    => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        // Transforma em JSON e retorna
        echo json_encode($retorno);
    }
}


?>