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
namespace CnabPHPBank\resources\generico\retorno\L040;

use CnabPHPBank\RegistroRetAbstract;
use CnabPHPBank\RetornoAbstract;
use Exception;

class Generico1 extends RegistroRetAbstract
{

  protected $counter = 0;

  protected function set_codigo_lote($value)
  {
    $this->data['codigo_lote'] = RetornoAbstract::$loteCounter;
  }

  public function set_tipo_servico($value)
  {
    if ($value == 'S') {
      $this->data['tipo_servico'] = 1;
    } elseif ($value == 'N') {
      $this->data['tipo_servico'] = 2;
    } elseif ((int) $value <= 2) {
      $this->data['tipo_servico'] = $value;
    } else {
      throw new Exception("O tipo de servico deve ser 1 ou S para Registrada ou 2 ou N para Sem Registro, o valor informado foi:" . $value);
    }
  }

  protected function set_tipo_inscricao($value)
  {
    $value = $value ? $value : RetornoAbstract::$entryData['tipo_inscricao'];
    if ($value == 1 || $value == 2) {
      $this->data['tipo_inscricao'] = $value;
    } else {
      throw new Exception("O tipo de inscrição deve ser 1  para CPF e 2 para CNPJ, o valor informado foi:" . $value);
    }
  }

  protected function set_numero_inscricao($value)
  {
    $this->data['numero_inscricao'] = $value == '' ? str_ireplace(array('.', '/', '-'), array(''), RetornoAbstract::$entryData['numero_inscricao']) : str_ireplace(array('.', '/', '-'), array(''), $value);
  }

  protected function set_codigo_beneficiario($value)
  {
    $this->data['codigo_beneficiario'] = RetornoAbstract::$entryData['codigo_beneficiario'];
  }


  protected function set_codigo_beneficiario_dv($value)
  {
    $this->data['codigo_beneficiario_dv'] = RetornoAbstract::$entryData['codigo_beneficiario_dv'];
  }

  protected function set_agencia($value)
  {
    $this->data['agencia'] = $value == '' ? RetornoAbstract::$entryData['agencia'] : $value;
  }

  protected function set_agencia_dv($value)
  {
    $this->data['agencia_dv'] = $value == '' ? RetornoAbstract::$entryData['agencia_dv'] : $value;
  }

  protected function set_conta($value)
  {
    $this->data['conta'] = $value == '' ? RetornoAbstract::$entryData['conta'] : $value;
  }

  protected function set_conta_dv($value)
  {
    $this->data['conta_dv'] = $value == '' ? RetornoAbstract::$entryData['conta_dv'] : $value;
  }

  protected function set_codigo_convenio($value)
  {
    $this->data['codigo_convenio'] = RetornoAbstract::$entryData['codigo_beneficiario'];
  }

  protected function set_nome_empresa($value)
  {
    $this->data['nome_empresa'] = $value == '' ? RetornoAbstract::$entryData['nome_empresa'] : $value;
  }

  protected function set_numero_remessa($value)
  {
    $this->data['numero_remessa'] = $value == '' ? RetornoAbstract::$entryData['numero_sequencial_arquivo'] : $value;
  }

  protected function set_mensagem_fixa1($value)
  {
    $mensagem = (isset($this->entryData['mensagem'])) ? explode(PHP_EOL, $this->entryData['mensagem']) : array();
    $this->data['mensagem_fixa1'] = count($mensagem) >= 1 ? $mensagem[0] : ' ';
  }

  protected function set_mensagem_fixa2($value)
  {
    $mensagem = (isset($this->entryData['mensagem'])) ? explode(PHP_EOL, $this->entryData['mensagem']) : array();
    $this->data['mensagem_fixa2'] = count($mensagem) >= 2 ? $mensagem[1] : ' ';
  }

  protected function set_data_gravacao($value)
  {
    $this->data['data_gravacao'] = date('Y-m-d');
  }

  public function get_counter()
  {
    $this->counter++;
    return $this->counter;
  }

  public function inserirDetalhe($data)
  {
    $class = 'CnabPHPBank\resources\\B' . RetornoAbstract::$banco . '\retorno\\' . RetornoAbstract::$layout . '\Registro3T';
    $this->children[] = new $class($data);
  }

  public function getText()
  {
    $retorno = '';
    $dataReg5 = array();
    $dataReg5['qtd_titulos_simples'] = '0';
    $dataReg5['qtd_titulos_caucionada'] = '0';
    $dataReg5['qtd_titulos_descontada'] = '0';
    $dataReg5['vrl_titulos_simples'] = '0.00';
    $dataReg5['vlr_titulos_caucionada'] = '0.00';
    $dataReg5['vlr_titulos_descontada'] = '0.00';

    foreach ($this->meta as $key => $value) {
      $retorno .= $this->$key;
    }
    RetornoAbstract::$retorno[] = $retorno;
    if ($this->children) {
      // percorre todos objetos filhos
      foreach ($this->children as $child) {
        if ($child->codigo_carteira == 1) {
          $dataReg5['qtd_titulos_simples']++;
          $dataReg5['vrl_titulos_simples'] += $child->getUnformated('valor');
        }
        if ($child->codigo_carteira == 3) {
          $dataReg5['qtd_titulos_caucionada']++;
          $dataReg5['vlr_titulos_caucionada'] += $child->getUnformated('valor');
        }
        if ($child->codigo_carteira == 4) {
          $dataReg5['qtd_titulos_descontada']++;
          $dataReg5['vlr_titulos_descontada'] += $child->getUnformated('valor');
        }
        $child->getText();
      }
      $class = 'CnabPHPBank\resources\\B' . RetornoAbstract::$banco . '\retorno\\' . RetornoAbstract::$layout . '\Registro5';
      $registro5 = new $class($dataReg5);
      $registro5->getText();
    }
  }
}