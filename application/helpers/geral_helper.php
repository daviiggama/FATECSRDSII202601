<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*função p/ verificar se os parâmetros vindos do front são os mesmos utilizados dentro das controllers */
function verificarParam($atributos, $lista) {
    $estatus = 1;

    //verifica se os elementos do front estão nos atributos necessários
    foreach ($lista as $key => $value) {
        if (array_key_exists($key, get_object_vars($atributos))) {
        $estatus = 1;
    } else {
        $estatus = 0;
        break;
    }
}

    //verifica a qtdade de elementos
    if (count(get_object_vars($atributos)) != count($lista)) {
        $estatus = 0;
    }

    return $estatus;
}

/* func p verificar os tipos de dados no inserir e alterar */
function validarDados($valor, $tipo, $tamanhoZero = true) {

    // verifica vazio ou nulo
    if (is_null($valor) || $valor == '') {
        return array('codigoHelper'=> 3, 'msg' => 'Conteúdo zerado.');
    }

    switch ($tipo) {
        case 'int':
            if (filter_var($valor, FILTER_VALIDATE_INT) === false) {
                return array('codigoHelper' => 4, 'msg' => 'Conteúdo não inteiro.');
            } break;

        case 'string':
            if(!is_string($valor) || trim($valor) === '') {
                return array('codigoHelper' => 5, 'msg'=> 'Conteúdo não é um texto.');
            }
            break;

        case 'date':
            if (!preg_match('/ˆ(\d{4})-(\d{2})-(\d{2})$/', $valor, $match)) {
                return array('codigoHelper' => 6, 'msg' => 'Data em formato inválido.');
            } else {
                $d = DateTime::createFromFormat('d-m-Y', $valor);
                if (($d->format('d-m-Y') === $valor) == false) {
                    return array ('codigoHelper'=> 6, 'msg' => 'Data inválida.');
                }
            }
            break;

        case 'hora':
            if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $valor)) {
                return array('codigoHelper'=> 7, 'msg' => 'Hora em formato inválido');
    }
    break;

    default:
            return array('codigoHelper' => 0, 'msg' => 'Tipo de dado não definido.');
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}

/*
 * Função para verificar os tipos de dados para Consulta
 * Diferença: se o valor estiver vazio, não valida, somente os que tiverem preenchidos
 */
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

            default:
                return array('codigoHelper' => 97, 'msg' => 'Tipo de dado não definido.');
        }
    }

    return array('codigoHelper' => 0, 'msg' => 'Validação correta.');
}

/*
 * Função para verificar se datas ou horários iniciais são maiores
 * que os finais
 */
function compararDataHora($valorInicial, $valorFinal, $tipo) {

    // Convertemos a string para hora
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

?>