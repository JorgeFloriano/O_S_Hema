
function getScrollHeight(elm){
   var savedValue = elm.value
   elm.value = ''
   elm._baseScrollHeight = elm.scrollHeight
   elm.value = savedValue
 }

 window.onload = function() {
   var textareas = document.querySelectorAll('.autoExpand');
   for (var i = 0; i < textareas.length; i++) {
     onExpandableTextareaInput({ target: textareas[i] });
   }
 };
 
 function onExpandableTextareaInput({ target:elm }){
   // make sure the input event originated from a textarea and it's desired to be auto-expandable
   if( !elm.classList.contains('autoExpand') || !elm.nodeName == 'TEXTAREA' ) return
   
   var minRows = elm.getAttribute('data-min-rows')|0, rows;
   !elm._baseScrollHeight && getScrollHeight(elm)
 
   elm.rows = minRows
   rows = Math.ceil((elm.scrollHeight - elm._baseScrollHeight) / 20)
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

//border-color:#fe8686;outline:0;box-shadow:0 0 0 .25rem rgba(253, 13, 13, 0.25)
function formSubmit(form) {
   document.getElementById(form).submit();
}
 
function scrollToBottom() {
   var height = document.body.scrollHeight;
   window.scroll(0 , height);
}

function getAddress() {
   let cep = document.querySelector('#cep').value
   if (cep.length !== 8) {
       alert('Cep inválido !')
       return
   }
   let url = `https://viacep.com.br/ws/${cep}/json/`
   fetch(url).then(function(response){
       response.json().then(showAddress)  
   })
}
function showAddress(dados) {
   console.log(dados)
   if (dados.erro) {
       window.alert("Cep não encontrado")
       return
   }
   document.querySelector('#address').value = dados.logradouro+', '+dados.bairro+', '+dados.localidade+' - '+dados.uf;
}

function enableDisable(id) {
   div = document.getElementById(id);
   if (div.disabled == false) {
      this.checked = false;
      div.checked = false;
      div.disabled = true;
   } else {
      this.checked = true;
      div.disabled = false;
   }
}

function toggleClientFields() {
   const saveRadio = document.getElementById('save');
   const finishedRadio = document.getElementById('finished');
   const clientFieldsDiv = document.getElementById('clientFields');
 
   if (finishedRadio.checked) {
     clientFieldsDiv.style.display = 'block';
     window.scrollTo(0, clientFieldsDiv.offsetTop); // Add this line
   } else {
     clientFieldsDiv.style.display = 'none';
   }
 }

 // global delegated event listener
 document.addEventListener('input', onExpandableTextareaInput)

 // input datalist client get only client id, not name, acept only datalist option values
 function getOptId(inp_list, data_id, data_opt) {
   const inp_list_val = document.getElementById(inp_list).value;
   const regex = /\[(\d+)\]/;
   const match = inp_list_val.match(regex);
   const data_options = window.document.getElementsByClassName(data_opt);
   var opt_valid = false;

   for (let data_opt of data_options) {
      if (data_opt.value == inp_list_val) {
         opt_valid = true
      }
   }
   
   if (!opt_valid || !match) {
      document.getElementById(data_id).value = ''
      document.getElementById(inp_list).value = ''
      return
   }

   const client_id = inp_list_val.match(/\[(\d+)\]/)[1];
   document.getElementById(data_id).value = client_id
 }
