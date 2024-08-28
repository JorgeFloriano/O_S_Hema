<?php 
    function opt ($i, $msg) { ?>
        <section class="item" id="<?= $i?>" name="txt<?= $i?>">
            <section class="ask">
                <div class="lab">
                    <?= $i?>-<?= $msg?>.
                </div>
                <div class="opt">
                    <input type="radio" hidden name="txt<?= $i?>" id="<?= $i?>_OK" value="Ok" >
                    <i onclick="check('i<?= $i?>Ok', '<?= $i?>_OK', 'i<?= $i?>R', '<?= $i?>_R', 'i<?= $i?>T', '<?= $i?>_T', 'ok', 'secJust<?= $i?>' ,'idJust<?= $i?>')"
                    id="i<?= $i?>Ok" style="font-size: 18px;" class="fa fa-thumbs-up" ></i>
                    <input type="radio" hidden name="txt<?= $i?>" id="<?= $i?>_R" value="Recuperar" >
                    <i onclick="check('i<?= $i?>R', '<?= $i?>_R', 'i<?= $i?>Ok', '<?= $i?>_OK', 'i<?= $i?>T', '<?= $i?>_T', 're', 'secJust<?= $i?>' ,'idJust<?= $i?>')"
                    id="i<?= $i?>R" style="font-size: 18px;" class="fa fa-wrench"></i>
                    <input type="radio" hidden name="txt<?= $i?>" id="<?= $i?>_T" value="Trocar" >
                    <i onclick="check('i<?= $i?>T', '<?= $i?>_T', 'i<?= $i?>R', '<?= $i?>_R', 'i<?= $i?>Ok', '<?= $i?>_OK', 'tr', 'secJust<?= $i?>' ,'idJust<?= $i?>')"
                    id="i<?= $i?>T" style="font-size: 18px;" class="fa fa-thumbs-down"></i>
                </div>
            </section>
            <section style="display: none;" class="obs" name="txtSecJust<?= $i?>" id="secJust<?= $i?>">
                <label style="margin-right: 100px;" for="idJust<?= $i?>">Justificativa:</label>
                <textarea name="txtJust<?= $i?>" id="idJust<?= $i?>" class='autoExpand' rows='1' data-min-rows='1'></textarea>
            </section>
        </section>
    <?php }

    function reportElement($i, $descriptions) {
        foreach ($descriptions as $descr) {
            if (!isset($_POST['txt'.$i])) {
                $_POST['txt'.$i] = "Não insp.";
            } else {
                if ($_POST['txt'.$i] == null || $_POST['txt'.$i] == "") {
                    $_POST['txt'.$i] = "Não insp.";
                }
            }
            echo 
            "<tr>
                <td style='width: 4%;'>".$i."</td><td>".$descr."</td><td>".$_POST['txt'.$i]."</td>
            </tr>";
            $i++;
        }
    }
    function isEmpty($i, $msg = "Não insp.") {
        if ($_POST['txt'.$i] == null || $_POST['txt'.$i] == "" || !isset($_POST['txt'.$i])) {
            return $msg;
        }
        return $_POST['txt'.$i];
    }
?>
