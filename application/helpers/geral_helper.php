<?php
defined('BASEPATH') or exit('No direct script access allowed');

function verificarParam($atributos, $lista) {
    $estatus = 1;
    foreach ($lista as $key => $value) {
        if (array_key_exists($key, get_object_vars($atributos))) {
            $estatus = 1;
        } else {
            $estatus = 0;
            break;
        }
    }
    if (count(get_object_vars($atributos)) != count($lista)) {
        $estatus = 0;
    }
    return $estatus;
}

function validarDados($valor, $tipo, $tamanhoZero = true) {
    if (is_null($valor) || $valor == '') {
        return array('codigoHelper' => 3, 'msg' => 'Conteúdo zerado.');
    }

    switch ($tipo) {
        case 'int':
            if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
                return array('codigoHelper' => 4, 'msg' => 'Conteúdo não inteiro.');
            }
            break;

        case 'string':
            if (!is_string($valor) || trim($valor) === '') {
                return array('codigoHelper' => 5, 'msg' => 'Conteúdo não é um texto.');
            }
            break;

        case 'date':
            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valor, $match)) {
                return array('codigoHelper' => 6, 'msg' => 'Data em formato inválido.');
            } else {
                $d = DateTime::createFromFormat('Y-m-d', $valor);
                if (($d->format('Y-m-d') === $valor) == false) {
                    return array('codigoHelper' => 6, 'msg' => 'Data inválida.');
                }
            }
            break;

        case 'hora':
            if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $valor)) {
                return array('codigoHelper' => 7, 'msg' => 'Hora em formato inválido.');
            }
            break;

        case 'email':
            if (filter_var($valor, FILTER_VALIDATE_EMAIL) === false) {
                return array('codigoHelper' => 8, 'msg' => 'E-mail em formato inválido.');
            }
            break;

        default:
            return array('codigoHelper' => 0, 'msg' => 'Tipo de dado não definido.');
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}

function validarDadosConsulta($valor, $tipo) {
    if ($valor != '') {
        switch ($tipo) {
            case 'int':
                if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
                    return array('codigoHelper' => 4, 'msg' => 'Conteúdo não inteiro.');
                }
                break;

            case 'string':
                if (!is_string($valor) || trim($valor) === '') {
                    return array('codigoHelper' => 5, 'msg' => 'Conteúdo não é um texto.');
                }
                break;

            case 'date':
                if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valor, $match)) {
                    return array('codigoHelper' => 6, 'msg' => 'Data em formato inválido.');
                } else {
                    $d = DateTime::createFromFormat('Y-m-d', $valor);
                    if (($d->format('Y-m-d') === $valor) == false) {
                        return array('codigoHelper' => 6, 'msg' => 'Data inválida.');
                    }
                }
                break;

            case 'hora':
                if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $valor)) {
                    return array('codigoHelper' => 7, 'msg' => 'Hora em formato inválido.');
                }
                break;

            case 'email':
                if (filter_var($valor, FILTER_VALIDATE_EMAIL) === false) {
                    return array('codigoHelper' => 8, 'msg' => 'E-mail em formato inválido.');
                }
                break;

            default:
                return array('codigoHelper' => 97, 'msg' => 'Tipo de dado não definido.');
        }
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}

function compararDataHora($valorInicial, $valorFinal, $tipo) {
    $valorInicial = strtotime($valorInicial);
    $valorFinal   = strtotime($valorFinal);

    if ($valorInicial != '' && $valorFinal != '') {
        if ($valorInicial > $valorFinal) {
            switch ($tipo) {
                case 'hora':
                    return array('codigoHelper' => 13, 'msg' => 'Hora Final menor que a Hora Inicial.');
                    break;

                case 'data':
                    return array('codigoHelper' => 14, 'msg' => 'Data Final menor que a Data Inicial.');
                    break;

                default:
                    return array('codigoHelper' => 97, 'msg' => 'Tipo de verificação não definida.');
            }
        }
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11) {
        return array('codigoHelper' => 9, 'msg' => 'CPF inválido.');
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return array('codigoHelper' => 9, 'msg' => 'CPF inválido.');
    }

    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return array('codigoHelper' => 9, 'msg' => 'CPF inválido.');
        }
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}
?>