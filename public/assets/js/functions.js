
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

// form submit---------------------------------------------------------------------------------------------------------------------------
function formSubmit(form) {
   document.getElementById(form).submit();
}
 
function scrollToBottom() {
   var height = document.body.scrollHeight;
   window.scroll(0 , height);
}

// Get address-----------------------------------------------------------------------------------------------------------------------------
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

// CEP-------------------------------------------------------------------------------------------------------------------------------------
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

// Toggle client fields----------------------------------------------------------------------------------------------------------------------
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

 // input datalist client get only client id, not name, acept only datalist option values---------------------------------------------------
 function getOptId(inp_shown, inp_hidden, class_opt, index) {
   // inp_shown -> Input id where the user will select the option
   // inp_hidden -> Input id hidden where from the value of the selected option id will be stored (probably the id)
   // class_opt -> Class of the option, to verify if the option is valid
   // index -> If thi is called in a loop, the index was needed of the current iteration
   const inp_shown_val = document.getElementById(inp_shown).value;

   // Verify if the user want to keep input empty
   if (inp_shown_val == '') {
      if (inp_shown == 'tec') {
         document.getElementById(inp_hidden).value = '' 
      } else {
         document.getElementById(inp_hidden).value = '0'
         document.getElementById(inp_shown).value = ''
      }
      if (index == '' || index == null) {
         return
      }
   }

   // Get the last number of the selected option beteen [], to get the id
   const matches = inp_shown_val.match(/\[(\d+)\](?!.*\[\d+\])/);

   // Verify if the option is valid
   const data_options = window.document.getElementsByClassName(class_opt);
   var opt_valid = false;
   for (let class_opt of data_options) {
      if (class_opt.value == inp_shown_val) {
         opt_valid = true
      }
   }
   
   // If the option is valid but dont find the last number, id will be null and return
   if (opt_valid && matches == null) {
      document.getElementById(inp_hidden).value = null
      if (index == '' || index == null) {
         return
      }
   }

   if (matches == null) {
      document.getElementById(inp_hidden).value = '0'
      document.getElementById(inp_shown).value = ''
      if (index == '' || index == null) {
         return
      }
   }

   // Get the id, if is valid, else id = 0
   var object_selected_id = 0
   if (matches != null) {
      object_selected_id = matches[1]
   }
   document.getElementById(inp_hidden).value = object_selected_id

   // If this is called in a loop, update tec_id in order
   if (index != '' && index != null) {

      // Update tec_id in order , without reload page with fetch
      updateOrderTec(index, object_selected_id)
   }
 }
 
 // Manage list-----------------------------------------------------------------------------------------------------------------
 function manageList(object) {
   const select = document.getElementById(object);
   const selectedOption = select.options[select.selectedIndex];
   let descr = selectedOption.innerHTML;

   if (select.selectedIndex == 0) {
      return
   }

   // Verify if object is already added
   if (document.querySelector('.'+object +'-added-descr') !== null) {
      const options = document.getElementsByClassName(object +'-added-descr');
      
      for (let option of options) {
         if (option.innerHTML == descr) {
            select.selectedIndex = 0; // reset select
            document.getElementById(object).blur();
            alert('Ítem já adicionado, utilize o campo numérico para determinar a quantidade, digitando zero ou deixando o campo vazio o ítem será removido !')
            return
         }
      }
   }

   // Verify if object description is empty, return
   if ( descr == '' ) {
      select.selectedIndex = 0; // reset select
      document.getElementById(object).blur(); // remove focus
      return
   }

   // Instace variables
   const obj_selected_id = document.getElementById(object).value;
   const obj_selected_unit = selectedOption.getAttribute('data-unit'); // object_selected_unit
   const form = document.getElementById('form');

   // Call the function to create elements to constitute the datalist items
   createListItemElements(descr, object, obj_selected_id, '1', obj_selected_unit);
   
   document.getElementById(object).blur();// remove focus
   getValuesAndSetInput(object);// Call the function to get values and set them in the result input
   select.selectedIndex = 0; // reset select
 }

 // Function to get values fro the several inputs and set them in a input separated by commas-----------------------
 function getValuesAndSetInput(object) {
   // Select all elements that was added in list
   const elements = document.querySelectorAll('.'+object+'-added');

   // Extract their values into an array
   const values = Array.from(elements).map(element => element.value);

   // Join the values into a comma-separated string
   const commaSeparatedValues = values.join(',');

   // Set the result in the target input field
   document.getElementById(object+'s_array').value = commaSeparatedValues;
}

// Function to create elements to constitute the datalist items------------------------------------------------------------
function createListItemElements(descr, object, obj_selected_id, obj_selected_qud, obj_selected_unit) {
   // Create div from the object selected
   const div = document.createElement('div');
   const object_list = document.getElementById(object+'_list');
   object_list.style.background = '#e9ecef';
   object_list.style.border = '1px solid #ced4da';
   object_list.style.borderRadius = '.25rem';
   object_list.classList.add('my-2');
   div.classList.add('input-group');
   div.id = object +'_'+obj_selected_id+'_div';
   object_list.appendChild(div);

   // Add span with object description-
   
   const objDscr = document.createElement('span');
   objDscr.classList.add('input-group-text');
   objDscr.classList.add(object +'-added-descr');
   objDscr.innerHTML = descr;
   objDscr.style.width = '70%';
   objDscr.style.background = 'transparent';
   objDscr.style.border = 'none';
   div.appendChild(objDscr);
   objDscr.id = object +'_'+obj_selected_id+'_descr';

   // Add input with object quantity
   const objInput = document.createElement('input');
   objInput.type = 'number';
   objInput.name = object +'_'+obj_selected_id+'_qtd';
   objInput.classList.add('form-control');
   objInput.classList.add(object +'-added-qtd');
   objInput.style.border = 'none';
   objInput.placeholder = 'Quantidade';
   objInput.required = true;
   objInput.autocomplete = 'off';
   objInput.value = obj_selected_qud;
   objInput.min = 0;
   objInput.id = object +'_'+obj_selected_id+'_qtd';
   div.appendChild(objInput);

   // Add input hidden with object id in value
   const objSelectedId = document.createElement('input');
   objSelectedId.type = 'hidden';
   objSelectedId.name = object +'_'+obj_selected_id;
   objSelectedId.classList.add(object +'-added');
   objSelectedId.value = obj_selected_id;
   objSelectedId.min = 0;
   objSelectedId.id = object +'_'+obj_selected_id;
   div.appendChild(objSelectedId);

   // Add span with unit of measurement of the object
   const objUnit = document.createElement('span');
   objUnit.classList.add('input-group-text');
   objUnit.innerHTML = obj_selected_unit;
   objUnit.style.width = '12%';
   objUnit.style.background = 'transparent';
   objUnit.style.border = 'none';
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

         // Select all elements that was added in list
         const elements = document.querySelectorAll('.'+object+'-added');
         if (elements.length < 1) {
            const object_list = document.getElementById(object+'_list');
            object_list.style.background = 'none';
            object_list.style.border = 'none';
            object_list.style.borderRadius = 'none';
            object_list.classList.remove('my-2');
        }
         getValuesAndSetInput(object);// Call the function to get values and set them in the result input
      }
   });
}

// Function to submit form-------------------------------------------------------------------------------------------
function submitRoute(route, form, msg) {
   if (msg != null && msg != '') {
      alert(msg);
      return
   }
   document.getElementById(form).action = route;
   document.getElementById(form).submit();
}

// Function to clear inputs-----------------------------------------------------------------------------------------
function clearInputs(input, inputHidden, msg) {
   document.getElementById(input).value = '';
   document.getElementById(inputHidden).value = msg;
}

// Update tec_id in order , without reload page with fetch----------------------------------------------------------
function updateOrderTec(index, object_selected_id) {
   const orderId = index;
   const tecId = object_selected_id;

   fetch(`/Hema/public/orders/${orderId}/ord_tec_update`, {
      method: 'POST',
      headers: {
            'Content-Type': 'application/json', // Include JSON header
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Include CSRF token
      },
      body: JSON.stringify({ tec_id: tecId }) // Send JSON data
   })
   
   .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao atualizar técnico.');
   });
}
