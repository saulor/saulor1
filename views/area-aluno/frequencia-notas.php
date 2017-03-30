<!-- Banner -->
<section id="banner" class="hidden-xs">
  <div id="banner-wrap">
    <img alt="" title="" data-description="" src="<?php echo Url::templatePath(); ?>images/banners/area-aluno.png" />
  </div>
</section>
<!-- Banner -->

<!-- Área do Aluno  -->
<section id="area-aluno">
  <div class="wrap-section">
    <div class="container">

      <div class="row no-gutter">
        <div class="col-lg-12">
          <div class="navegacao-wrap hidden-xs">
            <ol class="breadcrumb">
              <li><a href="<?php echo SITEURL; ?>">Início</a></li>
              <li><a href="<?php echo SITEURL; ?>area-aluno">Área do Aluno</a></li>
              <li class="active">Frequência e Notas</li>
            </ol>
          </div>
        </div>
      </div> 

      <div class="row">
          <div class="col-lg-12">
           <div class="heading internas" id="topo">
              <h1>Área do Aluno</h1>
              <h2>Frequência e Notas</h2>
              <span class="bottomline"></span>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <p>Clique <a href="<?php echo SITEURL; ?>area-aluno/sair">aqui</a> para sair da Área do Aluno.</p>
        </div>
      </div>

      <br />

      <style>
        table {
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
          font-family: "Helvetica Neue", Helvetica, Arial;
          color: #3b3b3b;
          -webkit-font-smoothing: antialiased;
          font-smoothing: antialiased;
        }
        table thead {
          color: #ffffff;
          background: #ea6153;
        }
        thead th {
          vertical-align: middle !important;
        }
      </style>

      <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped hidden-sm hidden-xs">
              <thead>
                <tr>
                  <th>Curso</th>
                  <th>Disciplina/Professor</th>
                  <th colspan="2" style="text-align: center;">Data(s) da(s) Aula(s)</th>
                  <th style="text-align: center;">Nota</th>
                  <th style="text-align: center;">Nota Substitutiva</th>
                  <th style="text-align: center;">Data Reposição</th>
                  <th style="text-align: center;">Faltas</th>
                  <th>Situação</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($data['dados']) == 0) { ?>
                <tr><td colspan="9" align="center">Nenhum registro encontrado</td></tr>
                <?php } else { foreach ($data['dados'] as $dado) { ?>
                <tr>
                  <td><?php echo $dado->nomeCurso; ?></td>
                  <td><?php echo $dado->nomeDisciplina . '<br />' . $dado->professor; ?></td>
                  <td align="center"><?php echo $dado->dataInicioF; ?></td>
                  <td align="center"><?php echo $dado->dataFimF; ?></td>
                  <td align="center"><?php echo (float) ($dado->notaAluno/10); ?></td>
                  <td align="center"><?php echo (float) ($dado->notaSubstituida/10); ?></td>
                  <td align="center"><?php echo $dado->dataReposicao; ?></td>
                  <td align="center"><?php echo $dado->numeroFaltas; ?></td>
                  <td align="center"><?php echo $dado->situacaoNota; ?></td>
                </tr>
                <?php }} ?>
              </tbody>
            </table>

            <?php if (count($data['dados']) == 0) { ?>
            <table class="table table-striped visible-sm visible-xs">
            <tr><td colspan="9" align="center">Nenhum registro encontrado</td></tr>
            </table>
            <?php } else { 
                $cursos = array();
                $cursos[] = $dado->nomeCurso;

                //echo '<p>' . $dado->nomeCurso . '</p>';
                echo '<table class="table table-striped visible-sm visible-xs">';
                echo '<thead><tr><th>' . $dado->nomeCurso . '</th></tr></thead>';

                foreach ($data['dados'] as $dado) {
                  echo '<tr><td>';
                  echo $dado->nomeDisciplina;
                  if (!empty($dado->professor)) {
                    echo '<br /><small>' . $dado->professor . '</small>';
                  }

                  if (!empty($dado->dataInicioF) || !empty($dado->dataFimF)) {
                    $datas = array($dado->dataInicioF, $dado->dataFimF);
                    echo '<br /><small>' . implode(' - ', $datas) . '</small>';
                  }
                  
                  
                  echo '<br /><br /><strong>Nota:</strong> ' . (float) ($dado->notaAluno/10);
                  if (!empty($dado->dataReposicao)) {
                    echo '<br /><strong>Data Reposição:</strong> ' . $dado->dataReposicao;
                    echo '<br /><strong>Nota Substitutiva:</strong> ' . (float) ($dado->notaSubstituida/10);
                  }
                  echo '<br /><strong>Faltas:</strong> ' . $dado->numeroFaltas;
                  echo '<br /><strong>Situação:</strong> ' . $dado->situacaoNota;
                  echo '</td></tr>';

                  if (!in_array($dado->nomeCurso, $cursos)) {
                    $cursos[] = $dado->nomeCurso;
                    echo '</table>';
                    echo '<table class="table table-striped visible-sm visible-xs">';
                    echo '<thead><tr><th>' . $dado->nomeCurso . '</th></tr></thead>';
                  } 
                }

                echo '</table>';
              } 
            ?>

            <?php if (count($data['dados']) > 20) { ?>
              <a href="#topo">
                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> topo
              </a>
            <?php } ?>

        </div>
      </div>
    </div>
  </div>
</section>
<!-- Área do Aluno  -->