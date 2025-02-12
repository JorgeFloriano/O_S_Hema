
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
   const matches = inp_list_val.match(/\[(\d+)\](?!.*\[\d+\])/);
   const data_options = window.document.getElementsByClassName(data_opt);
   var opt_valid = false;

   for (let data_opt of data_options) {
      if (data_opt.value == inp_list_val) {
         opt_valid = true
      }
   }
   
   if (!opt_valid || !matches) {
      document.getElementById(data_id).value = '0'
      document.getElementById(inp_list).value = ''
      return
   }

   const object_selected_id = matches[1]
   document.getElementById(data_id).value = object_selected_id
 }
 
 // Manage list
 function manageList(object) {
   const input = document.getElementById(object);
   const descr = input.value;

   // Verify if object is already added
   if (document.querySelector('.'+object +'-added-descr') !== null) {
      const options = document.getElementsByClassName(object +'-added-descr');
      
      for (let option of options) {
         if (option.innerHTML == descr ) {
            document.getElementById(object).value = '';
            document.getElementById(object).blur();
            alert('Ítem já adicionado, utilize o campo numérico para determinar a quantidade, digitando zero ou deixando o campo vazio o ítem será removido !')
            return
         }
      }
   }

   // Verify if object description is empty, return
   if ( descr == '' ) {
      document.getElementById(object).blur(); // remove focus
      return
   }

   // Instace variables
   const inp_list_val = document.getElementById(object).value;
   const matches = inp_list_val.match(/\[(\d+)\](?!.*\[\d+\])/);
   const obj_selected_id = matches[1]; // object_selected_id 
   const obj_selected_unit = document.getElementById(obj_selected_id+'_unit').value; // object_selected_unit
   const form = document.getElementById('form');

   // Call the function to create elements to constitute the datalist items
   createListItemElements(descr, object, obj_selected_id, obj_selected_unit, 'serv');
   
   document.getElementById(object).blur();// remove focus
   getValuesAndSetInput(object);// Call the function to get values and set them in the result input
 }

 // Function to get values and set them in the result input
 function getValuesAndSetInput(object) {
   // Select all elements with the class "form-control"
   const elements = document.querySelectorAll('.'+object+'-added-id');

   // Extract their values into an array
   const values = Array.from(elements).map(element => element.value);

   // Join the values into a comma-separated string
   const commaSeparatedValues = values.join(',');

   // Set the result in the target input field
   document.getElementById(object+'_ids_array').value = commaSeparatedValues;
}

// Function to create elements to constitute the datalist items
function createListItemElements(descr, object, obj_selected_id, obj_selected_unit, ref_pos_elem_id) {
   // Create div from the object selected
   const div = document.createElement('div');
   div.classList.add('input-group');
   div.classList.add('my-2');
   div.id = object +'_'+obj_selected_id+'_div';

   // Add div with object to form, inserting before the element with id "serv"
   const referencePositionElement = document.getElementById(ref_pos_elem_id);
   form.insertBefore(div, referencePositionElement);

   // Add span with object description
   const objDscr = document.createElement('span');
   objDscr.classList.add('input-group-text');
   objDscr.classList.add(object +'-added-descr');
   objDscr.innerHTML = descr ;
   objDscr.style.width = '70%';
   div.appendChild(objDscr);
   objDscr.id = object +'_'+obj_selected_id+'_descr';

   // Add input with object quantity
   const objInput = document.createElement('input');
   objInput.type = 'number';
   objInput.name = object +'_'+obj_selected_id+'_qtd';
   objInput.classList.add('form-control');
   objInput.classList.add(object +'-added-qtd');
   objInput.placeholder = 'Quantidade';
   objInput.required = true;
   objInput.autocomplete = 'off';
   objInput.value = 1;
   objInput.min = 0;
   objInput.id = object +'_'+obj_selected_id+'_qtd';
   div.appendChild(objInput);

   // Add input hidden with object id in value
   const objSelectedId = document.createElement('input');
   objSelectedId.type = 'hidden';
   objSelectedId.name = object +'_'+obj_selected_id+'_id';
   objSelectedId.classList.add(object +'-added-id');
   objSelectedId.value = obj_selected_id;
   objSelectedId.min = 0;
   objSelectedId.id = object +'_'+obj_selected_id+'_id';
   div.appendChild(objSelectedId);

   // Add span with unit of measurement of the object
   const objUnit = document.createElement('span');
   objUnit.classList.add('input-group-text');
   objUnit.innerHTML = obj_selected_unit;
   objUnit.style.width = '12%';
   objUnit.id = object +'_'+obj_selected_id+'_unit';
   div.appendChild(objUnit);

   // Add an event listener to remove all elements when the input value is zero or empty
   objInput.addEventListener('change', function () {
      if (this.value == 0 || this.value == '') { // Check if the value is zero
         div.remove(); // Remove the div element
         objDscr.remove(); // Remove the span with object description
         objInput.remove(); // Remove the input with object quantity
         objUnit.remove(); // Remove the span with unit of measurement
         objSelectedId.remove(); // Remove the input hidden with object id
         getValuesAndSetInput(object);// Call the function to get values and set them in the result input
      }
   });
}