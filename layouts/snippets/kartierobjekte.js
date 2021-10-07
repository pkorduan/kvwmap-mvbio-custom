/*
$('.datensatz ').each(function(i) {
	var layer_id = $(this).attr('layer_id'),
			oid = $(this).attr('oid'),
			i = $(this).attr('dataset_form_index'),
			kampagne_field = $('#' + layer_id + '_kampagne_id_' + i),
			old_namekampagne_form_field_name = layer_id + ';id;kampagnen;;Auswahlfeld_not_saveable;1;int4;1',
			kampagne_id = kampagne_field.next().val();

	$(this).attr('name', layer_id);

105;id;kampagnen;;Auswahlfeld_not_saveable;1;int4;1: 13

<input type="hidden" name="105;id;kampagnen;;Auswahlfeld_not_saveable;1;int4;1" class="" onchange="set_changed_flag(currentform.changed_105_);" value="13">

		kampagne_field.after('<input type="hidden" name="' + kampagne_form_field_name + '" value="' + kampagne_id + '"></input>');
		$('input[name="form_field_names"]').val($('input[name="form_field_names"]').val() + '|' + kampagne_form_field_name);
});
*/
// Schaltet die Auswahl der Verlustbögen bei der Neuerfassung auch aus wenn eine andere Kartierebene gewählt wird.
$('#105_kartierebene_id_-1').on(
  'change',
  function() {
    setTimeout(function() {
      $('#value_105_bogenart_id_-1').children().children('option[value="3"]').hide();
    }, 1000);
  }
);

$('#tr_105_fotos_0').on(
  'change',
  function() {
    var num_fotos = $('#105_fotos_0 div.raster_record,div.raster_record_open').length;
    $('#105_foto_0').val(num_fotos);
    $('#105_foto_0').next().html(num_fotos);
  }
);

if ($('#105_bogenart_id_0').val() == 1) {
  $('.105_group_FFH-LRT, .105_group_Beschreibung, .105_group_Standortmerkmale, .105_group_Nutzungsmerkmale, .105_group_Pflanzen').hide();
}

wert_krit_selectors = [];
for (i = 1; i <= 16; i++) {
	wert_krit_selectors.push('#105_wert_krit_' + i + '_0');
}

$(wert_krit_selectors.join(', ')).on(
	'mouseup',
	function(evt) {
		if (!$(evt.target).is(':checked')) {
			$('#105_keine_wert_krit_0').removeAttr("checked");
		}
	}
);

$('#105_keine_wert_krit_0').on(
	'change',
	function(evt) {
		if ($(evt.target).is(':checked')) {
			$(wert_krit_selectors.join(', ')).removeAttr("checked");
		}
	}
);

function getTextfieldId(layerId, datensatzNr, variableName) {
	return layerId+"_"+variableName+"_"+datensatzNr;
}


function DatensatzValidator(layerId, datensatzNr) {
	// console.info("layerid=" + layerId + " datensatzNr=" + datensatzNr);
	this.datensatzNr = datensatzNr;
	this.layerId = layerId;
	
	this.map = {};
	
	this.fct;

	this.bogenartIdChanged=function(evt) {
		this.hideElementsByRecordCreation();
	}

	this.start=function() {
		label = this.getValue("label");
		if (!label || label.length === 0) {
			// console.info("new record");
			this.hideElementsByRecordCreation();
		}
		else {
			// console.info("not new record");
			this.disableDropbox(document.getElementById(this.layerId + "_kampagne_id_" + datensatzNr));
			this.disableDropbox(document.getElementById(this.layerId + "_kartierebene_id_" + datensatzNr));

			this.disableDropbox(document.getElementById(this.layerId + "_kartiergebiet_id_" + datensatzNr));
			this.disableDropbox(document.getElementById(this.layerId + "_bogenart_id_" + datensatzNr));
			this.disableDropbox(document.getElementById(this.layerId + "_lrt_gr_" + datensatzNr));
		}

		this.startCheckCodeSum();
		this.startValidateGefcodes();
		
	}
	
	/**
	 * Startet Validierung Gefährdungen: entweder sind Gefährdungen eingegeben oder Checkbox "Keine Gefährdungen" ist gewählt.
	 */
	this.startValidateGefcodes = function() {		
		this.observeSubForm("gefcode", this.observerSubFormGefcode);
		this.appendListener("ohnegefahr", this.validateGefcodes);
		this.appendListener("gefaehrdg", this.validateGefcodes);
		
		
		this.validateGefcodes();
	}
	
	this.validateGefcodes = function() {
		var gefcodeCount = 0; 
		
		var elem = document.getElementById(this.layerId+"_gefcode_"+datensatzNr);

		var elems = elem.getElementsByClassName("subFormListItem");
		if (elems && elems.length) {
			gefcodeCount = elems.length;
		}
		
		var ohneGefahr = this.getValue("ohnegefahr");
		var textGefahr = this.getValue("gefaehrdg");
		
		if (gefcodeCount>0 || (textGefahr && textGefahr.length>0)) {
			this.disable("ohnegefahr", true);
		}
		else {
			this.disable("ohnegefahr", false);
		}
		
		//console.info("validateGefcodes "+gefcodeCount+" ohneGefahr="+ohneGefahr);
		//console.info("validateGefcodes textGefahr: \""+textGefahr+"\"");
	}
	
	this.observerSubFormGefcode = function(mutationRecords) {
		this.validateGefcodes();
	}	
	
	
	/**
	 * Startet das Aufsummieren der prozentuellen Flächenanteile
	 */
	this.startCheckCodeSum = function() {
		let fctValidateCodeSum = this.checkCodeSum.bind(this);

		var elem = document.getElementById(this.layerId+"_hc_"+datensatzNr);
		if (elem) {
			elem.addEventListener("change", fctValidateCodeSum);
		}
		
		
		
		elem = document.getElementById(this.layerId+"_hcp_"+datensatzNr);
		if (elem) {
			elem.addEventListener("keyup", fctValidateCodeSum);
		}

		this.observeSubForm("nc", this.observerSubFormHC);
		
		
		elem = document.getElementById(this.layerId+"_nc_"+datensatzNr);
		let textElem = document.createElement("span");
		elem.parentElement.appendChild(textElem);
		this.txtFlaechenAnteile = textElem;

		
		this.checkCodeSum();
		
	}

	this.disableDropbox = function(elem) {
//		console.log(elem.name, elem.id, elem.tagName, elem.type);
//		return;
		let hiddenElem = document.createElement("input");
		hiddenElem.type = "hidden";
		hiddenElem.id = elem.id;
		hiddenElem.name = elem.name;
		hiddenElem.value = elem.value;
		elem.parentNode.appendChild(hiddenElem);

		let lblElem = document.createElement("label");
		lblElem.innerText = elem.value;
		elem.replaceWith(lblElem);
		console.log('replace');
	}

	this.hideElementsByRecordCreation = function() {
		this.hide("105_group_Angaben_zur_Erstkartierung");
		this.hide("105_group_Biotoptypen");
		this.hide("105_group_Lage");

		this.hide("105_group_Lage"); // ToDo: Warum ist das doppelt?
		this.hide("105_group_Beschreibung");
		this.hide("105_group_Wertbestimmende_Kriterien");
		this.hide("105_group_Gefährdung");
		this.hide("105_group_Empfehlung");
		this.hide("105_group_Pflanzen");
		this.hide("105_group_Standortmerkmale");
		this.hide("105_group_FFH-LRT");
		this.hide("105_group_Nutzungsmerkmale");
		this.hide("105_group_Pflanzen");
		this.hide("105_group_Tiere");
		this.hide("105_group_Bearbeitung");
		this.hide("105_group_Fotos");
		this.hide("105_group_Rest");
		this.hide("105_group_Schutzmerkmale");


		$('#value_105_bogenart_id_' + datensatzNr).children().children('option[value="3"]').hide();
		document.getElementById("tr_105_bearbeitungsstufe_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_pruefhinweis_koordinator_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_biotopname_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_standort_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_la_sper_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_la_sper_"+datensatzNr).style.display = 'none';
		document.getElementById("tr_105_fb_id_"+datensatzNr).style.display = 'none';
	}


	this.hide = function(className) {
		let elems = document.getElementsByClassName(className);
		if (elems) {
			for (let i=0; i<elems.length; i++) {
				elems[i].style.display = 'none';
				elems[i].className = elems[i].className + " display_none";
			}
		}

	}
	
	this.observerSubFormHC = function(mutationRecords) {
		if (mutationRecords && mutationRecords.length) {
			for (let i=0; i<mutationRecords.length; i++) {
				let tbls = mutationRecords[i].target.getElementsByTagName("table");
				let tblC = (tbls && tbls.length) ? tbls.length : 0;
			}
		}
		let elem = document.getElementById(this.layerId+"_nc_"+datensatzNr);
		let elems = elem.getElementsByClassName("subFormListItem");
		this.checkCodeSum();
	}

	this.observeSubForm = function(variableName, fct) {
		let fctObserve = fct.bind(this);
		let config = { attributes: true, childList: true, subtree: true };
		let observer = new MutationObserver(fctObserve);
		elem = document.getElementById(this.layerId+"_"+variableName+"_"+this.datensatzNr);

		if (elem && elem.parentNode) {
			observer.observe(elem, config);
		}

	}

	this.checkCodeSum = function(evt) {
		
		let summeFlaechenAnteile = parseInt(this.getValue("hcp"));
		
		if (evt && evt.target && evt.target.id.indexOf("hcp")>0 && evt.target.value ) {
			evt.target.value = evt.target.value.replace(/[^0-9]/g, '');
		}
				
		if (evt && evt.keyCode && evt.keyCode<46) {
			return;
		}

		let elem = document.getElementById(this.layerId+"_nc_"+datensatzNr);

		let elems = elem.getElementsByClassName("subFormListItem");
		if (elems && elems.length) {
			for (let i=0; i<elems.length; i++) {
				let entryCell = elems[i];
				let linkElements = entryCell.getElementsByTagName("a");
				if (linkElements && linkElements.length===1) {
					let sA = linkElements[0].innerText.split(" ");
					let sNr = sA[sA.length-2];
					
					let value = Number(sNr);
					if (value<0) {
						message([{ "type" : 'error', "msg" : "Wert kleiner 0 ("+linkElements[0].innerText+")"}]);
					}
					summeFlaechenAnteile += value;
				}
				
			}
		}

		if (isNaN(summeFlaechenAnteile)) {
			this.txtFlaechenAnteile.innerText="Summe Flächenanteile konnte nicht berechnet werden";
		}
		else {
			if (summeFlaechenAnteile>100) {
				message([{ "type" : 'warning', "msg" : 'Die Summe der Flächenanteile ist größer 100'}]);
			}
			this.txtFlaechenAnteile.innerText="Summe Flächenanteile: "+summeFlaechenAnteile+" %";
		}
		
	}

	this.onClick=function(event) {
		console.info("onClick "+this.layerId+":"+this.datensatzNr);
		
		var kartEb = getValue("kartierebene_id");
		if (kartEb===2) {
			var lrtCode = getValue("lrt_code");
			var  lrt_gr = getValue("lrt_gr");
			console.info(lrt_gr+"  "+lrtCode);
		}
		event.preventDefault();
		event.stopPropagation();
		// this.fct.call();
	}
	
	this.getParent=function(child, parentTag) {
		if (child.parentNode) {
			parent = child.parentNode;
			if (parent.tagName===parentTag) {
				return parent;
			}
			return this.getParent(parent, parentTag);
		}
	}
	
	this.getNextSiblingWithTag=function(node, tagName) {
		var nextSibling;
		if (node.nextSibling) {
			nextSibling = node.nextSibling;
			// console.info(parent.tagName);
			if (nextSibling.tagName===tagName) {
				return nextSibling;
			}
			return this.getNextSiblingWithTag(nextSibling, tagName);
		}
	}

	this.addElement=function(inputElement) {
		var variableName = inputElement.id;
		if (variableName && variableName.length>0) {
			var idx = variableName.indexOf("_");
			if (idx>=0) {
				variableName = variableName.substring(idx+1);
				idx = variableName.lastIndexOf("_");
				if (idx>=0) {
					variableName = variableName.substring(0, idx);
					if (inputElement.type!="text" && inputElement.tagName.toLowerCase()!="select") {
						idx = variableName.lastIndexOf("_");
						if (idx>0) {
							variableName = variableName.substring(0, idx);
						}
					}
				}
			}
			if (!this.map[variableName]) {
				this.map[variableName]=[];
			}
			try {
				this.map[variableName].push(inputElement);
			}
			catch {
				console.info(inputElement.id);
			}
		}
				
	}
	
	this.getBoolValue = function(valueName) {
		var k = valueName.toLowerCase();
		var sValue,value,id,idA,ele;
		var elements = this.map[k];
		var result;
		var multiChoices = false;
		if (!elements || elements.length !== 2) {
			throw new Error("Elements kein Boolean bei getValue(\""+valueName+"\") "+elements);
		}
		else {
			if (elements[0].checked) {
				return elements[0].value === true || elements[0].value === "t";
			}
			if (elements[1].checked) {
				return elements[1].value === true || elements[1].value === "t";
			}
		}
		return  -1;
	}	
	
	
	this.appendListener = function(attributeName, handler) {
		var ele,i;
		var k = attributeName.toLowerCase();
		var elements = this.map[k];
		
		var hdl = handler.bind(this);
		if (elements) {
			for (var i=0; i<elements.length; i++) {
				ele = elements[i];
				ele.addEventListener('change', hdl);
				if (ele.type === 'text' || ele.type === 'textarea') {
					ele.addEventListener('keyup', hdl);
				}
			}
		}
	}
	

	this.getValue = function(valueName) {
		var k = valueName.toLowerCase();
		var sValue,value,id,idA,ele;
		var elements = this.map[k];
		var result = [];
		var multiChoices = false;
		if (!elements || elements.length===0) {
			console.info("elements null oder leer bei getValue(\""+valueName+"\") "+elements);
		}
		if (elements) {
			if (elements.length===1) {
				if (elements[0].type==="checkbox") {
					return elements[0].checked;
				}
				return elements[0].value;
				// console.info("getValue "+k+"  "+this.datensatzNr+"  => ["+elements[0].value+"] single");
			}
			else {
				for (var i=0; i<elements.length; i++) {
					multiChoices = elements[i].type==="checkbox";
					if (elements[i].checked) {
						ele = elements[i];
						if (ele.type==="radio") {
							sValue=ele.value;
						}
						if (ele.type==="checkbox") {
							id = elements[i].id;
							idA = id.split("_");
							sValue = idA[idA.length-2];
						}
						value=parseInt(sValue);
						result.push(value);
					}
				}
			}
		}
		if (multiChoices) {
			return result;
		}
		return result.length===1 ? result[0] : -1;
	}
	
	this.disable = function(valueName, disable) {
		
        var k = valueName.toLowerCase();
        var label,id,idA,ele,s;
        var elements = this.map[k];
        var result = [];
		var multiChoices = false;
		if (!elements || elements.length===0) {
			console.info("elements null oder leer bei getValue(\""+valueName+"\") "+elements);
		}
		if (elements) {
			for (var i=0; i<elements.length; i++) {
				ele = elements[i];
				ele.disabled=disable;
				if (disable && ele.checked) {
					ele.checked=false;
				}
			}
		}		
    }
}

function App(layerId) {
	
	this.layerId = layerId;

	this.map = {};

	this.parse=function(tagName) {
		var inputElements = document.querySelectorAll(tagName);
		for (var i = 0; i < inputElements.length; i++) {

			var id = inputElements[i].id;
			
			var idA = id.split("_");
			var datensatzNr = Number.parseInt(idA[idA.length-1], 10);
			var layerId = Number.parseInt(idA[0], 10);
			if (layerId==this.layerId) {			 
				if (!isNaN(datensatzNr) && !isNaN(layerId)) {
					if (!this.map[layerId+datensatzNr]) {
						this.map[layerId+datensatzNr]=new DatensatzValidator(layerId, datensatzNr);
					}
					this.map[layerId+datensatzNr].addElement(inputElements[i]);
				}
			}
		}
		

	}

	this.start=function() {
		this.parse("textarea");
		this.parse("input");
		this.parse("select");
		
		for (k in this.map) {
			this.map[k].start();
		}
	}

}