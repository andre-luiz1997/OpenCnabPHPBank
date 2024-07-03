<?php
/*
 * CnabPHPBank - Geração de arquivos de remessa e retorno em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Ciatec.net
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace CnabPHPBank;

use CnabPHPBank\RegistroAbstract;
use Exception;

abstract class RegistroRetAbstract extends RegistroAbstract
{
    protected $entryData;
    protected $counter;
    /**
     * Método __construct()
     * instancia registro qualquer
     * @$data = array de dados para o registro
     */
    public function __construct($data = NULL)
    {
        // log_message('debug', 'Classe ' . get_class($this) . ' instanciada');
        // log_message('debug', 'DATA ' . print_r($data, true));
        if ($data) { // se o ID for informado
            // carrega o objeto correspondente
            $this->entryData = $data;
            foreach ($this->meta as $key => $value) {
                $this->$key = (isset($data[$key])) ? $data[$key] : $this->meta[$key]['default'];
            }
        }
    }

    /**
     * Método __set()
     * executado sempre que uma propriedade for atribu?da.
     */
    public function __set($prop, $value)
    {
        // log_message('debug', 'A função ' . __FUNCTION__ . ' foi chamada para ' . $prop . ' = ' . $value . ';');
        // verifica se existe Método set_<propriedade>
        if (method_exists($this, 'set_' . $prop)) {
            // executa o Método set_<propriedade>
            call_user_func(array($this, 'set_' . $prop), $value);
        } else {
            $metaData = (isset($this->meta[$prop])) ? $this->meta[$prop] : null;
            if (($value == "" || $value === NULL) && $metaData['default'] != "") {
                $this->data[$prop] = $metaData['default'];
            } else {
                // atribui o valor da propriedade
                $this->data[$prop] = $value;
            }
        }
    }

    /**
     * Método __get()
     * executado sempre que uma propriedade for requerida
     */
    public function __get($prop)
    {
        // verifica se existe Método get_<propriedade>
        if (method_exists($this, 'get_' . $prop)) {
            // executa o Método get_<propriedade>
            return call_user_func(array($this, 'get_' . $prop));
        } else {
            return $this->___get($prop);
        }
    }

    /**
     * Método ___get()
     * metodo auxiliar para ser chamado para dentro de metodo get personalizado
     */
    public function ___get($prop)
    {
        // log_message('debug', 'A função ' . __FUNCTION__ . ' foi chamada por ' . debug_backtrace()[1]['function'] . ' na instância ' . get_class($this));
        // log_message('debug', 'Propriedade ' . $prop . ';');
        // retorna o valor da propriedade
        if (isset($this->meta[$prop])) {
            $metaData = (isset($this->meta[$prop])) ? $this->meta[$prop] : null;
            $this->data[$prop] = !isset($this->data[$prop]) || $this->data[$prop] == '' ? $metaData['default'] : $this->data[$prop];
            if ($metaData['required'] == true && ($this->data[$prop] == '' || !isset($this->data[$prop]))) {
                if (isset($this->data['nosso_numero'])) {
                    // log_message('debug', 'Valor faltante: ' . print_r($this->data[$prop], true));
                    // log_message('debug', 'Classe: ' . get_class($this));
                    throw new Exception('Campo faltante ou com valor nulo:' . $prop . " Boleto Numero:" . $this->data['nosso_numero']);
                } else {
                    throw new Exception('Campo faltante ou com valor nulo:' . $prop);
                }
            }
            set_error_handler(function ($severity, $message, $file, $line, $errcontext) {
                throw new \ErrorException(json_encode($errcontext) . "->" . $message, $severity, $severity, $file, $line);
            });

            switch ($metaData['tipo']) {
                case 'decimal':
                    // log_message('debug', 'DECIMAL prop ' . print_r($this->data[$prop], true) . ';');
                    $retorno = (($this->data[$prop] && trim($this->data[$prop]) !== "" ? number_format($this->data[$prop], $metaData['precision'], '', '') : (isset($metaData['default']) ? $metaData['default'] : '')));
                    return str_pad($retorno, $metaData['tamanho'] + $metaData['precision'], '0', STR_PAD_LEFT);
                case 'int':
                    // log_message('debug', 'INT prop ' . print_r($this->data[$prop], true) . ';');
                    $retorno = (isset($this->data[$prop]) && trim($this->data[$prop]) !== "" ? number_format($this->data[$prop], 0, '', '') : (isset($metaData['default']) ? $metaData['default'] : ''));
                    return str_pad($retorno, $metaData['tamanho'], '0', STR_PAD_LEFT);
                case 'alfa':
                    // log_message('debug', 'alfa prop ' . print_r($this->data[$prop], true) . ';');
                    $retorno = (isset($this->data[$prop])) ? $this->prepareText($this->data[$prop]) : '';
                    return $this->mb_str_pad(mb_substr($retorno, 0, $metaData['tamanho'], "UTF-8"), $metaData['tamanho'], ' ', STR_PAD_RIGHT);
                case 'alfa2':
                    $retorno = (isset($this->data[$prop])) ? $this->data[$prop] : '';
                    return $this->mb_str_pad(mb_substr($retorno, 0, $metaData['tamanho'], "UTF-8"), $metaData['tamanho'], ' ', STR_PAD_RIGHT);
                case $metaData['tipo'] == 'date' && $metaData['tamanho'] == 6:
                    $retorno = ($this->data[$prop]) ? date("dmy", strtotime($this->data[$prop])) : '';
                    return str_pad($retorno, $metaData['tamanho'], '0', STR_PAD_LEFT);
                case $metaData['tipo'] == 'date' && $metaData['tamanho'] == 8:
                    $retorno = ($this->data[$prop]) ? date("dmY", strtotime($this->data[$prop])) : '';
                    return str_pad($retorno, $metaData['tamanho'], '0', STR_PAD_LEFT);
                case $metaData['tipo'] == 'dateReverse':
                    $retorno = ($this->data[$prop]) ? date("Ymd", strtotime($this->data[$prop])) : '';
                    return str_pad($retorno, $metaData['tamanho'], '0', STR_PAD_LEFT);
                default:
                    return null;
            }
            restore_error_handler();
        }
    }

    public function getFileName()
    {
        return 'R' . RetornoAbstract::$banco . str_pad('U123456', 6, '0', STR_PAD_LEFT) . '.ret';
    }
}
