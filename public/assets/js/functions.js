
function check(c1, ch, c2, u1, c3, u2, opt, obs, txt, just) {
   var col = document.getElementById(c1).style.color;
   var che = document.getElementById(ch).checked;
   const green = "#198754";
   const gray = "rgb(200, 200, 200)";
   const red = "#dc3545";
   var checked_color = green;
  
   document.getElementById(c2).style.color = gray;
   document.getElementById(u1).checked = false;
   document.getElementById(c3).style.color = gray;
   document.getElementById(u2).checked = false;
   if ( opt == 'ok') {
      document.getElementById(txt).required = false;
      document.getElementById(obs).style.display = 'none';
      document.getElementById(txt).placeholder = "Desceva como a pendência foi solucionada!";
      //document.getElementById(txt).value = '';
   } if (opt == 'tr') {
      document.getElementById(obs).style.display = 'block';
      document.getElementById(txt).required = true;
      document.getElementById(txt).placeholder = "Razão da recomendação para substituição o ítem";
      checked_color = red;
      if (c1 == "i52T") {
         document.getElementById(txt).value = "Substituir reparos da botoeira.";
      }
   } if (opt == 're') {
      document.getElementById(obs).style.display = 'block';
      document.getElementById(txt).required = true;
      document.getElementById(txt).placeholder = "Razão da recomendação para reparação do ítem";
      checked_color = 'rgb(50, 50, 50)';
   }
   if(che == true && col != gray ) {
      document.getElementById(c1).style.color = gray;
      document.getElementById(ch).checked = false;
      document.getElementById(txt).required = false;
      document.getElementById(obs).style.display = 'none';
      //document.getElementById(txt).value = '';
   } else {
      document.getElementById(c1).style.color = checked_color;
      document.getElementById(ch).checked = true;
   }
   if (document.getElementById(txt).value != '' || just != '') {
      document.getElementById(obs).style.display = 'block';
      document.getElementById(txt).required = true;
      document.getElementById(txt).placeholder = "Descreva a tualização da pendência anterior!";
      //document.getElementById(obs).style.background = 'rgb(239, 239, 174)';
   }
}

function displayon (obs, item) {
   document.getElementById(obs).style.display = 'block';
   document.getElementById(item).style.marginBottom = '0px';
   document.getElementById(item).style.borderRadius = '5px 5px 0px 0px';
   
}

function displayoff (obs, item, txt) {
   document.getElementById(obs).style.display = 'none';
   document.getElementById(item).style.borderRadius = '5px';
   document.getElementById(txt).value = '';
}

function desmarcar (x) {
   document.getElementById(x).setAttribute('checked', '');
}

function marcDesm(x) {
   if (x == document.getElementById(x).id) {
       var ch = document.getElementById(x).checked;
       if(ch == true) {
           document.getElementById(x).checked = false;
       }
       else
           document.getElementById(x).checked = true;
   } else {
       document.getElementById(x).checked = false;
   }
}

function getScrollHeight(elm){
   var savedValue = elm.value
   elm.value = ''
   elm._baseScrollHeight = elm.scrollHeight
   elm.value = savedValue
 }
 
 function onExpandableTextareaInput({ target:elm }){
   // make sure the input event originated from a textarea and it's desired to be auto-expandable
   if( !elm.classList.contains('autoExpand') || !elm.nodeName == 'TEXTAREA' ) return
   
   var minRows = elm.getAttribute('data-min-rows')|0, rows;
   !elm._baseScrollHeight && getScrollHeight(elm)
 
   elm.rows = minRows
   rows = Math.ceil((elm.scrollHeight - elm._baseScrollHeight) / 22)
   elm.rows = minRows + rows
 }
 
 function blockScreen() {
   document.querySelector('html').style.overflow = "hidden";
 }

 function unlockScreen() {
   document.querySelector('html').style.overflow = "";
 }

 function limitDate(d, hi, hf) {
   var date = document.getElementById(d);
   var hini = document.getElementById(hi);
   var hfin = document.getElementById(hf);
   var z_left_h = "";
   var z_left_m = "";
   dataAtual = new Date();
   const hor = dataAtual.getHours();
   const min = dataAtual.getMinutes();
   if (date.value == date.max) {
      if (hor < 10) {
         z_left_h = "0";
      }
      if (min < 10) {
         z_left_m = "0";
      }
      hini.max = z_left_h + hor + ":" + z_left_m + min;
      hfin.max = z_left_h + hor + ":" + z_left_m + min;
   } else {
      hini.max = '';
      hfin.max = '';
   }
 }

 function requiredItem() {
   let show_message = false;
   for (let index = 1; index < 67; index++) {
      if (document.getElementById(index) !== null ) {
         let item_class = document.getElementById("idItem"+index).className;
         if (item_class == 'important') {
            var bom_cor = document.getElementById(index + "_OK").checked;
            var reg_cor = document.getElementById(index + "_R").checked;
            var tro_cor = document.getElementById(index + "_T").checked;
            if (bom_cor == reg_cor && bom_cor == tro_cor) {
               show_message = true;
            }
         }
      }
   }
   if (show_message == true) {
      document.getElementById('submit_button').type = "button";
      document.getElementById('reqItem').innerHTML = "Os ítens com asterisco vermelho são críticos e de inspeção obrigatória, pois estão relacionados à segurança!! No caso de estarem em condições ruins, recomendar o bloqueio do equipamento!<br>";
   } else {
      window.document.getElementById('submit_button').type = "submit";
      document.getElementById('reqItem').innerHTML = "";
   }
 }

 // Important item, if bad txtContadorApto++, if good or more or less txtContadorApto--
 function naoApto(clicked, i) {

   // If element exists
   if (document.getElementById(i) !== null ) {
      let item_class = document.getElementById("idItem"+i).className;

      // If important item
      if (item_class == 'important') {

         var t_id = 'i'+i+'T'

         var t_checked = document.getElementById(i+'_T').checked

         var tcont = document.getElementById("contApto")
         var cont = Number(tcont.value)

         if (clicked.id == t_id && t_checked == false) { 

            cont++
            document.getElementById("contApto").value = cont
            //window.alert(clicked.id)
         }
         if (t_checked == true) {

            cont--
            document.getElementById("contApto").value = cont
         }
         if (cont > 0) {
            document.getElementById('nApto').checked = true;
            document.getElementById("nAptoAlert").innerHTML = "EQUIPAMENTO ESTÁ COM " + String(cont) + " ÍTENS CRÍTICOS EM CONDIÇÕES RUÍNS, RECOMENDA-SE O BLOQUEIO DO MESMO!!"
            document.getElementById("nAptoAlert").hidden = false;
         } else {
            document.getElementById('nApto').checked = false;
            document.getElementById("nAptoAlert").hidden = true;
         }
      }
   }
 }

function limitMed(id_nom, id_lim, id_med) {
   var tmed = document.getElementById(id_med)
   var tlim = document.getElementById(id_lim)
   var tnom = document.getElementById(id_nom)
   var tcont = document.getElementById("contApto")
   

   var med = Number(tmed.value)
   var lim = Number(tlim.innerHTML)
   var nom = Number(tnom.innerHTML)
   var cont = Number(tcont.value)
   var bord_color = window.getComputedStyle(document.getElementById(id_med),null).getPropertyValue('border-color')

   if (med != 0) {
      if ((nom < lim && lim < med) || ( nom > lim && lim > med)) {
         // If border isn't red, turns red
         if (bord_color != 'rgb(255, 0, 0)') {
            cont++
            tmed.style.setProperty('--inputBorder', '1px solid red');
            tmed.style.setProperty('--focusBoxShadow', '0 0 0 .25rem rgba(253, 13, 13, 0.25)');
            document.getElementById("contApto").value = cont;
         }
           
         // If border is red ,turns blue again
      } else {
         if (bord_color == 'rgb(255, 0, 0)') {
            cont--
            tmed.style.setProperty('--inputBorder', '1px solid #86b7fe');
            tmed.style.setProperty('--focusBoxShadow', '0 0 0 .25rem rgba(13,110,253,.25)');
            document.getElementById("contApto").value = cont
         } 
      }
      if (cont > 0) {
         document.getElementById('nApto').checked = true;
         document.getElementById("nAptoAlert").innerHTML = "EQUIPAMENTO ESTÁ COM " + String(cont) + " ÍTENS CRÍTICOS EM CONDIÇÕES RUÍNS, RECOMENDA-SE O BLOQUEIO DO MESMO!!"
         document.getElementById("nAptoAlert").hidden = false;
      } else {
         document.getElementById('nApto').checked = false;
         document.getElementById("nAptoAlert").hidden = true;
      }
   }
}

// Tec_create / Tec_edit
function autoFillMax (clicked, nom, max) {
   var t_nom = document.getElementById(nom).value;
   var t_max = document.getElementById(max).value;
   var n_nom = Number(t_nom);
   var n_max = Number(t_max);

   if ((t_nom != '' && t_max != '') && (n_nom > n_max)) {
      document.getElementById(clicked.id).type = "button"
      window.alert("Alguns valores dos campos NOMINAL e LIMITE estão errados ou invertidos, verifique e tente novamente!")
   } else {
      window.document.getElementById(clicked.id).type = "submit"
   }

   if (t_nom != '' && t_max == '') {
      t_max = t_nom * 1.111;
      document.getElementById(max).value = t_max.toFixed(2);
   }

   if (t_nom == '' && t_max != '') {
      t_nom = t_max * 0.9;
      document.getElementById(nom).value = t_nom.toFixed(2);
   }
}

function autoFillMin (clicked, nom, min) {
   var t_nom = document.getElementById(nom).value;
   var t_min = document.getElementById(min).value;
   var n_nom = Number(t_nom);
   var n_min = Number(t_min);

   if ((t_nom != '' && t_min != '') && (n_nom < n_min)) {
      document.getElementById(clicked.id).type = "button"
      window.alert("Alguns valores dos campos NOMINAL e LIMITE estão errados ou invertidos, verifique e tente novamente!")
   } else {
      window.document.getElementById(clicked.id).type = "submit"
   }

   if (t_nom != '' && t_min == '') {
      t_min = t_nom * 0.9;
      document.getElementById(min).value = t_min.toFixed(2);
   }

   if (t_nom == '' && t_min != '') {
      t_nom = t_min * 1.111;
      document.getElementById(nom).value = t_nom.toFixed(2);
   }
}

//border-color:#fe8686;outline:0;box-shadow:0 0 0 .25rem rgba(253, 13, 13, 0.25)
function formUpdate() {
   document.getElementById("form").submit();
}
 
function scrollToBottom() {
   var height = document.body.scrollHeight;
   window.scroll(0 , height);
}

 // global delegated event listener
 document.addEventListener('input', onExpandableTextareaInput)
 
 // OLD SOLUTION USING JQUERY:
 // // Applied globally on all textareas with the "autoExpand" class
 
 // $(document)
 //     .one('focus.autoExpand', 'textarea.autoExpand', function(){
 //         var savedValue = this.value;
 //         this.value = '';
 //         this._baseScrollHeight = this.scrollHeight;
 //         this.value = savedValue;
 //     })
 //     .on('input.autoExpand', 'textarea.autoExpand', function(){
 //         var minRows = this.getAttribute('data-min-rows')|0, rows;
 //         this.rows = minRows;
 //         rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 16);
 //         this.rows = minRows + rows;
 //     });
