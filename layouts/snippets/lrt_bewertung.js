// var aaaa;
// if (aaaa) { console.log('yes'); } else { console.log('nicht hgesettz'); }
var colorDisabled = "#bcd";
function test() {
	var foo;
	bar;
	return foo;
}

function isFloat(n) {
	// return Number(n) === n && n % 1 !== 0;
	return Number(n) === n;
}

function Frame(content, title, className) {
	this.content = content;
	this.title = title;
	this.active = false;

	this.dragStart = function (e) {
		if (e.type === "touchstart") {
			this.initialX = e.touches[0].clientX - this.dom.offsetLeft;
			this.initialY = e.touches[0].clientY - this.dom.offsetTop;
		} else {
			this.initialX = e.clientX - this.dom.offsetLeft;
			this.initialY = e.clientY - this.dom.offsetTop;
		}
		this.active = true;

		this.dragFct = this.drag.bind(this);
		this.dom.parentNode.addEventListener("mousemove", this.dragFct);
		this.dom.parentNode.addEventListener("touchmove", this.dragFct);
		this.dragEndFct = this.dragEnd.bind(this);
		this.dom.parentNode.addEventListener("mouseleave", this.dragEndFct);
	}
	this.dragEnd = function (e) {
		if (this.active) {
			this.active = false;
			this.dom.parentNode.removeEventListener("mousemove", this.dragFct);
			this.dom.parentNode.removeEventListener("touchmove", this.dragFct);
			this.dom.parentNode.removeEventListener("mouseleave", this.dragEndFct);
			this.dragFct = null;
			this.dragEndFct = null;
			this.dragEnded = true;
		}
	}
	this.drag = function (e) {
		if (this.active) {

			if (e.type === "touchmove") {
				clientX = e.touches[0].clientX - this.initialX;
				clientY = e.touches[0].clientY - this.initialY;
			} else {
				e.preventDefault();
				clientX = e.clientX - this.initialX;
				clientY = e.clientY - this.initialY;
			}
			if (clientX < 0) {
				clientX = 0;
			}
			if (clientY < 0) {
				clientY = 0;
			}
			if (clientX + this.dom.offsetWidth > this.dom.parentNode.offsetWidth) {
				clientX = this.dom.parentNode.offsetWidth - this.dom.offsetWidth;
			}
			if (clientY + this.dom.offsetHeight > this.dom.parentNode.offsetHeight) {
				clientY = this.dom.parentNode.offsetHeight - this.dom.offsetHeight;
			}
			this.dom.style.left = clientX + "px";
			this.dom.style.top = clientY + "px";
			if (window.localStorage) {
				window.localStorage.setItem('frame_x_' + this.title, clientX);
				window.localStorage.setItem('frame_y_' + this.title, clientY);
			}
		}

	}

	this.toggle = function () {

		if (this.content.style.display == 'none') {
			this.content.style.display = "block";
			this.closeBttn.innerHTML = '<img border="0" src="graphics//minus.gif">';
		} else {
			this.content.style.display = "none";
			this.closeBttn.innerHTML = '<img border="0" src="graphics//plus.gif">';
		}
	}

	this.dom = document.createElement("div");
	this.dom.className = "frame";
	this.top = document.createElement("div");
	this.top.style.position = "relative";
	this.top.style.heigth = "35px";

	this.closeBttn = document.createElement("a");
	// this.closeBttn.innerHTML = '<i id="frame_content_toggle_button" class="fa fa-minus-square-o" aria-hidden="true" style="float: right; margin: 3px 3px 2px 7px; color: black;"></i>';
	this.closeBttn.innerHTML = '<img border="0" src="graphics//minus.gif">';
	var spanTitle = document.createElement("span");
	spanTitle.className = "fett";
	spanTitle.innerText = title;
	this.top.appendChild(this.closeBttn);
	this.top.appendChild(spanTitle);
	this.dom.appendChild(this.top);
	this.dom.appendChild(content);

	this.fctToggle = this.toggle.bind(this);
	this.closeBttn.addEventListener("click", this.fctToggle);

	this.dom.addEventListener("touchstart", this.dragStart.bind(this));
	this.dom.addEventListener("touchend", this.dragEnd.bind(this));

	this.dom.addEventListener("mousedown", this.dragStart.bind(this), false);
	this.dom.addEventListener("mouseup", this.dragEnd.bind(this));

	if (window.localStorage) {
		var x = window.localStorage.getItem('frame_x_' + this.title);
		var y = window.localStorage.getItem('frame_y_' + this.title);
		if (x && y) {
			this.dom.style.left = x + "px";
			this.dom.style.top = y + "px";
		}
	}
}

function showText(s) {

	this.close = function () {

		document.body.removeChild(this.div);
	}
	this.div = document.createElement('div');
	document.body.appendChild(this.div);
	this.div.style.position = "fixed";
	this.div.style.top = "50px";
	this.div.style.left = "50px";
	this.div.style.zIndex = "999";
	this.div.style.width = "90%";
	this.div.style.height = "90%";
	// this.div.style.height="100px";
	this.div.style.backgroundColor = "#fff";
	// this.div.addEventListener("click", this.close.bind(this));

	var textArea = document.createElement('textarea');
	this.div.appendChild(textArea);

	textArea.innerHTML = s;
	textArea.style.height = "100%";
	textArea.style.width = "100%";
	textArea.addEventListener("mouseup", this.close.bind(this));
	textArea.style.fontFamily = "monospace";
	textArea.style.tabSize = "4";
	this.div.addEventListener("click", this.close.bind(this));
}

function getColor(s) {
	if (s === 'A') return "green";
	if (s === 'B') return "yellow";
	if (s === 'C') return "red";
	if (s === "unklar") return "lightblue";
	return "gray";
}


function getGesamtBewertung(bws) {

	var i, aBw = 0, bBw = 0, cBw = 0;
	for (i = 0; i < 3; i++) {
		if (bws[i] === 'A') aBw++;
		if (bws[i] === 'B') bBw++;
		if (bws[i] === 'C') cBw++;
	}
	console.info("getGesamtBewertung A=" + aBw + "  B=" + bBw + "  C=" + cBw)
	if ((aBw + bBw + cBw) === 3) {
		if (cBw > 1) {
			return "C";
		}
		if (cBw === 1) {
			return "B";
		}
		if (bBw > 1) {
			return "B";
		}
		else {
			return "A";
		}
	}
	return null;

}


function getTextfieldId(layerId, datensatzNr, variableName) {
	return layerId + "_" + variableName + "_" + datensatzNr;
}

function BewertungFrame(datensatzNr, bewertung) {
	this.datensatzNr = datensatzNr;
	this.div = document.createElement("div");
	this.div.className = 'bewertungsFrame';
	this.tbl = document.createElement("table");
	this.tbl.width = "160px";
	this.div.appendChild(this.tbl);

	this.map = {};

	this.bewertung = bewertung;
	bewertungen = bewertung.getBewertungen();

	// var grpNr;
	for (var i = 0; i < bewertungen.length; i++) {
		var bewertung = bewertungen[i];
		var row = document.createElement("tr");
		this.map[bewertung.nr] = row;
		let cell01 = document.createElement("td");
		cell01.innerText = bewertung.nr;
		cell01.width = "65%";
		cell01.title = bewertung.txt;
		// cell01.style.padding="13px";
		let cell02 = document.createElement("td");
		cell02.width = "35%";
		cell02.style.textAlign = "center";
		row.appendChild(cell01);
		row.appendChild(cell02);
		this.tbl.appendChild(row);
		var rowNr = i;
		var f = (function () {
			var rowNr = i;
			return function () {
				this.showBewertungsDetails(rowNr);
			}
		})();
		row.addEventListener("click", f.bind(this));
	}
	// this.div.style.position="fixed";
	// this.div.style.top="0px";
	// this.div.style.left="0px";

	this.showBewertungsDetails = function (bewertungsNr) {
		// console.info("nr="+bewertungsNr)
		// console.info(this.bewertung.bewertungen[bewertungsNr]);
		var s = "";
		for (k in this.bewertung.bewertungen[bewertungsNr]) {
			s += "\n" + k + ": ";
			s += this.bewertung.bewertungen[bewertungsNr][k];
		}
		// showText(this.bewertungen[bewertungsNr].bewerte.toString());
		showText(s);
	}


	this.setBewertung = function (bewertung) {
		var bewertungsNr = bewertung.nr;
		var value = bewertung.getResult();
		// console.info("setBewertung("+bewertungsNr+", "+value+")");
		var tbl = this.tbl; // document.getElementById("bewertungsFrame");
		var row = this.map[bewertungsNr];

		if (value === -1) {
			row.cells[1].innerText = "";
			row.style.backgroundColor = "#aaa";
		}
		else {
			if (value.skiped) {
				row.cells[1].innerText = "";
				row.cells[1].title = "Bewertung entfällt";
				row.style.backgroundColor = "#444";

			}
			else {
				if (bewertung.getKorrekturWert) {
					row.cells[1].innerHTML = value;
					// row.style.backgroundColor='lightblue';
					row.style.backgroundColor = getColor(bewertung.resultKorrigiert);
				}
				else {
					if (value === 0) {
						row.cells[1].innerText = "";
						row.title = "Die Eingangswerte für den Bewertungsalgorithmus ergeben keinen Zustand.";
						row.style.backgroundColor = "gray";
					} else {
						row.cells[1].innerText = value.b;
						row.style.backgroundColor = getColor(value.b);
					}
				}
			}
		}
	}


	var frame = new Frame(this.div, "Bewertungen", "bewertungsFrame");
	document.body.appendChild(frame.dom);
	// document.body.appendChild(this.div);
}



/* ----------------- */
function BewertungFliessgewaesser_1_1_1() {

	Bewertung.call(this);

	this.nr = "1.1.1";
	this.txt = "Fließgewässerstrukturgüte (Gesamtbewertung Sohle / Gesamtbewertung";
	this.lrtCodes = [3260];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T111_1 = b.getValue("T111_1");
		if (lrtCode === 3260) {
			if (T111_1 === 1) {
				result = { b: "A", txt: "Güteklasse 1" };
			}
			if (T111_1 === 2) {
				result = { b: "B", txt: "Güteklasse 2" };
			}
			if (T111_1 === 3) {
				result = { b: "C", txt: "Güteklasse 3" };
			}
		}
		this.result = result;
	}
}


function BewertungFliessgewaesser_1_2_1() {

	Bewertung.call(this);

	this.nr = "1.2.1";
	this.txt = "Vegetationsfreie Schlamm-, Sand- bzw. Kiesbänke und/oder schlammige, sandige bzw. kiesige Uferbereiche";
	this.lrtCodes = [3270];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T121_1 = b.getValue("T121_1");
		var T121_2 = b.getValue("T121_2");
		if (T121_1 > 0 && T121_2 > 0) {
			if (lrtCode === 3270) {
				if (T121_1 >= 1 && T121_2 >= 1 && T121_1 <= 3 && T121_2 <= 3) {
					if (T121_1 === 1 && T121_2 === 1) {
						result = { b: "A", txt: "jeweils mehrfach im Fließgewässer und am Ufer" };
					} else if (T121_1 === 3 || T121_2 === 3) {
						result = { b: "C", txt: "jeweils vereinzelt im Fließgewässer oder am Ufer" };
					} else {
						result = { b: "B", txt: "vereinzelt im Fließgewässer oder vereinzelt am Ufer" };
					}
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_2_1_1() {
	Bewertung.call(this);

	this.nr = "2.1.1";
	this.txt = "Gesamtanzahl lr-typischer und Anzahl besonders charakteristischer Pflanzenarten ";
	this.lrtCodes = [3260, 3270];

	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		let titleElement = b.getSectionTitleElement("T211_1_1");
		if (lrtCode === 3260) {
			titleElement.innerText = "2.1.1 Anzahl besonders charakteristischer Pflanzenarten";
			// console.info("value_145_t211_1_1_"+b.layerId);
			// console.info(document.getElementById("value_145_t211_1_1_"+b.datensatzNr));
			document.getElementById("value_145_t211_1_1_" + b.datensatzNr).style.display = 'none';
			document.getElementById("name_145_t211_1_1_" + b.datensatzNr).style.display = 'none';
		} else if (lrtCode === 3270) {
			titleElement.innerText = "2.1.1 Gesamtanzahl lebensraumtypischer Pflanzenarten";
			document.getElementById("value_145_t211_1_2_" + b.datensatzNr).style.display = 'none';
			document.getElementById("name_145_t211_1_2_" + b.datensatzNr).style.display = 'none';
		}
	}

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;

		if (lrtCode === 3260) {
			let T211_1_2 = parseInt(b.getValue("T211_1_2")); // Anzahl bes charakteristische Arten
			if (Number.isInteger(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (b.isBach()) {
					if (T211_1_2 >= 3) {
						result = { b: "A" };
					}
					if (T211_1_2 === 2) {
						result = { b: "B" };
					}
					if (T211_1_2 < 2) {
						result = { b: "C" };
					}
				} else if (b.isFluss()) {
					if (T211_1_2 >= 5) {
						result = { b: "A" };
					}
					if (T211_1_2 >= 3 && T211_1_2 < 5) {
						result = { b: "B" };
					}
					if (T211_1_2 < 3) {
						result = { b: "C" };
					}
				} else {
					result = { b: 0, err: "Wenn der Hauptcode nicht FBN, FBB, FBA, FFN, FFB oder FFA ist, kann die Bewertung nicht vorgenommen werden." };
				}
			}
		} else if (lrtCode === 3270) {
			let T211_1_1 = parseInt(b.getValue("T211_1_1")); // Gesamtzahl lrt-typisch
			if (Number.isInteger(T211_1_1) && T211_1_1 >= 0) {
				result = 0;
				if (T211_1_1 >= 10) {
					result = { b: "A" };
				}
				if (T211_1_1 >= 5 && T211_1_1 < 10) {
					result = { b: "B" };
				}
				if (T211_1_1 < 5) {
					result = { b: "C" };
				}
			}
		}
		this.result = result;
	}


}


function BewertungFliessgewaesser_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Tierarten";
	this.lrtCodes = [3260, 3270];

	this.getKorrekturWert = function () {
		console.info("BewertungFliessgewaesser_2_2.getKorrekturWert: " + this.korrekturWert);
		return this.korrekturWert;
	}

	this.berechneKorrekturWert = function (b) {
		var T22_1 = b.getValue("T22_1");
		var T22_2 = b.getTextValue("T22_2");
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
		console.info("BewertungFliessgewaesser_2_2.berechneKorrekturWert: " + this.korrekturWert);
	}
}


function BewertungFliessgewaesser_2_2_a() {

	Bewertung.call(this);

	this.nr = "2.2a";
	this.txt = "Fischfauna (Naturnähe der Ichtyozönose)";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T22A = parseInt(b.getValue("T22A"));
		if ([3260, 3270].indexOf(lrtCode) >= 0) {
			if (!isNaN(T22A) && T22A > 0 && (T22A <= 5 || T22A === 99)) {
				result = 0;
				if (T22A === 1) {
					result = { b: "A" };
				} else if (T22A === 2) {
					result = { b: "B" };
				} else if (T22A === 3 || T22A === 4 || T22A === 5) {
					result = { b: "C" };
				} else if (T22A === 99) {
					result = { skiped: true };
				}
			}
		}
		console.error("T22A=" + T22A + "  =>", result);
		this.result = result;
	}
}


function BewertungFliessgewaesser_2_2_b() {

	Bewertung.call(this);

	this.nr = "2.2b";
	this.txt = "Fischfauna (Abweichung des Arteninventars vom Referenzzustand)";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T22B = parseInt(b.getValue("T22B"));
		if ([3260, 3270].indexOf(lrtCode) >= 0) {
			if (!isNaN(T22B) && T22B > 0 && (T22B <= 5 || T22B === 99)) {
				result = 0;
				if (T22B === 5) {
					result = { b: "A" };
				} else if (T22B === 4 || T22B === 3) {
					result = { b: "B" };
				} else if (T22B === 1 || T22B === 2) {
					result = { b: "C" };
				} else if (T22B === 99) {
					result = { skiped: true };
				}
			}
		}
		console.error("T22B=" + T22B + "  =>", result);
		this.result = result;
	}
}


function BewertungFliessgewaesser_2_2_c() {

	Bewertung.call(this);

	this.nr = "2.2c";
	this.txt = "Makrozoobenthos (Güteklasse gesamt)";
	this.lrtCodes = [3260];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T22C = parseInt(b.getValue("T22C"));
		if ([3260].indexOf(lrtCode) >= 0) {
			if (!isNaN(T22C) && T22C > 0 && (T22C <= 5 || T22C === 99)) {
				result = 0;
				if (T22C === 1) {
					result = { b: "A" };
				} else if (T22C === 2) {
					result = { b: "B" };
				} else if (T22C === 3 || T22C === 4 || T22C === 5) {
					result = { b: "C" };
				} else if (T22C === 99) {
					result = { skiped: true };
				}
			}
		}
		console.error("T22C=" + T22C + "  =>", result);
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_1_1() {

	Bewertung.call(this);

	this.nr = "3.1.1";
	this.txt = "Strukturen zur Stoffeintragsminderung ";
	this.lrtCodes = [3260, 3270];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}

	this.berechneKorrekturWert = function (b) {
		let T311_1 = Number.parseInt(b.getValue("T311_1_1"));
		let T311_2 = b.getValue("T311_2");
		// console.info("BewertungFliessgewaesser_3_1_1.berechneKorrekturWert T311_2", T311_2);

		if (Number.isInteger(T311_2) && T311_2 > 0) {
			// 1       2       3       4       5        6
			// 100%    >90%    >75%    >50%    >=25%    <25% 
			let nrEingabe = Number.isInteger(T311_1);
			let isValid = !nrEingabe;
			if (nrEingabe) {
				isValid = T311_2 === 6 && T311_1 < 25 ||
					T311_2 === 5 && T311_1 >= 25 && T311_1 <= 50 ||
					T311_2 === 4 && T311_1 > 50 && T311_1 <= 75 ||
					T311_2 === 3 && T311_1 > 75 && T311_1 <= 90 ||
					T311_2 === 2 && T311_1 > 90 && T311_1 < 100 ||
					T311_2 === 1 && T311_1 === 100;

			}
			if (isValid) {
				if (T311_2 < 0) {
					this.korrekturWert = null;
				}
				else {
					this.korrekturWert = T311_2 < 3 ? 0 : -1;
				}
			}
			else {
				this.korrekturWert = { err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
			}
		}
		else {
			this.korrekturWert = null;
		}
		// console.info("T311_2", T311_2, this.korrekturWert);
	};

}

function BewertungFliessgewaesser_3_1_2() {

	Bewertung.call(this);

	this.nr = "3.1.2";
	this.txt = "Punktuelle Beeinträchtigung von Wasserkörper und Ufer (Abwassereinleitung)";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T312_1 = b.getValue("T312_1");
		if (T312_1 > 0) {
			result = 0;
			if (lrtCode === 3260) {
				if (b.isBach()) {
					if (T312_1 === 1) {
						result = { b: "A", txt: "keine Abwassereinleitungen" };
					}
					if (T312_1 === 2) {
						result = { b: "B", txt: "einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
					}
					if (T312_1 >= 3 && T312_1 <= 8) {
						result = { b: "C", txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
					}
				} else if (b.isFluss()) {
					if (T312_1 === 1 || T312_1 === 2) {
						result = { b: "A", txt: "keine Abwassereinleitungen, ausgenommen einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
					}
					if (T312_1 >= 3 && T312_1 <= 7) {
						result = { b: "B", txt: "Abwassereinleitungen > 50 EW vorhanden, jedoch Verhältnis MNQ zu Abwassermenge in l/s > 10, Einleitstelle liegt mindestens 4,5 km oberhalb" };
					}
					if (T312_1 === 8) {
						result = { b: "C", txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
					}
				} else {
					result = { b: 0, err: "Wenn der Hauptcode nicht FBN, FBB, FBA, FFN, FFB oder FFA ist, kann die Bewertung nicht vorgenommen werden." };
				}
			}
			if (lrtCode === 3270) {
				if (T312_1 === 1 || T312_1 === 2) {
					result = { b: "A", txt: "keine Abwassereinleitungen, ausgenommen einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
				}
				if (T312_1 >= 3 && T312_1 <= 7) {
					result = { b: "B", txt: "Abwassereinleitungen" };
				}
				if (T312_1 === 8) {
					result = { b: "C", txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_1_3() {

	Bewertung.call(this);

	this.nr = "3.1.3";
	this.txt = "Fischintensivhaltung (Rinnenanlagen, Fischteiche im Nebenschluss) ";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T313_1 = b.getBoolValue("T313_1");
		var T313_2 = b.getBoolValue("T313_2");
		if (T313_1 !== -1 && T313_2 != -1) {
			result = 0;
			if ([3260, 3270].indexOf(lrtCode) >= 0) {
				if (T313_1 || T313_2) {
					result = { b: "C", txt: "vorhanden" };
				}
				else {
					result = { b: "A", txt: "nicht vorhanden" };
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_2_1() {

	Bewertung.call(this);

	this.nr = "3.2.1";
	this.txt = "Wasserentnahme (bezogen auf Mittleren langjährigen Niedrigwasserabfluss = MNQ)";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var T321_1 = b.getBoolValue("T321_1");
		var T321_2 = b.getValue("T321_2");

		if (T321_1 !== -1 && (!T321_1 || T321_1 && T321_2 > 0)) {
			result = 0;
			if ([3260, 3270].indexOf(b.lrtCode) >= 0) {

				if (T321_1) {
					if (T321_2 === 1) {
						result = { b: "B", txt: "geringe Wasserentnahme: <= 1/3 MNQ" };
					}
					if (T321_2 === 2) {
						result = { b: "C", txt: "mengenmäßig bedeutende Wasserentnahme: > 1/3 MNQ" };
					}
				}
				else {
					result = { b: "A", txt: "keine" };
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_2_2() {

	Bewertung.call(this);

	this.nr = "3.2.2";
	this.txt = "Wasserkraftwerke";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;
		var T322_1 = b.getBoolValue("T322_1");
		if ([3260, 3270].indexOf(b.lrtCode) >= 0) {
			if (typeof T322_1 === "boolean") {
				if (T322_1) {
					result = { b: "C", txt: "vorhanden" };
				}
				else {
					result = { b: "A", txt: "keine" };
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_3_1() {

	Bewertung.call(this);

	this.nr = "3.3.1";
	this.txt = "Gewässerunterhaltung";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;

		var T331_1 = b.getBoolValue("T331_1");
		var T331_2 = b.getValue("T331_2");
		var T331_3 = b.getValue("T331_3");
		var T331_5 = b.getBoolValue("T331_5");
		this.disable(b, !T331_1);

		if ([3260, 3270].indexOf(b.lrtCode) >= 0) {
			if (typeof T331_1 === "boolean") {
				if (T331_1) {
					if (T331_2 > 0 && T331_3 > 0 && typeof T331_5 === "boolean") {
						result = 0;
						if (T331_5 === true || (T331_2 === 2 && T331_3 === 2)) {
							result = { b: "C", txt: "jährliche komplette Krautung von beiden Böschungen und Sohle" };
						}
						// if (T331_2 != 2 || T331_3 != 2) {
						else {
							result = { b: "B", txt: "jährliche Krautung von nur 1 Böschung und Sohle oder Krautung abschnittsweise" };
						}
					}
				}
				else {
					result = { b: "A", txt: "keine" };
				}
			}
		}
		this.result = result;
	}

	this.disable = function (b, disabled) {
		b.disable("T331_2", disabled);
		b.disable("T331_3", disabled);
		b.disable("T331_5", disabled);
	}
}

function BewertungFliessgewaesser_3_3_2() {

	Bewertung.call(this);

	this.nr = "3.3.2";
	this.txt = "Uferverbau";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;

		let T332_1 = Number.parseInt(b.getValue("T332_1"));
		let T332_2 = b.getValue("T332_2");
		if ([3260, 3270].indexOf(b.lrtCode) >= 0) {
			if (Number.isInteger(T332_2) && T332_2 > 0) {
				let nrEingabe = Number.isInteger(T332_1);
				let isValid = !nrEingabe;
				if (nrEingabe) {
					// 1        2        3          4           5
					// keine    <= 5%    > 5-25%    > 25-50%    > 50% 
					isValid = T332_2 === 5 && T332_1 > 50 ||
						T332_2 === 4 && T332_1 > 25 && T332_1 <= 50 ||
						T332_2 === 3 && T332_1 > 5 && T332_1 <= 25 ||
						T332_2 === 2 && T332_1 <= 5 && T332_1 > 0 ||
						T332_2 === 1 && T332_1 === 0;
					result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
				}
				if (isValid) {
					if (T332_2 > 0) {
						result = 0;
						if (T332_2 === 1) {
							result = { b: "A", txt: "keine Uferverbau" };
						}
						if (T332_2 === 2) {
							result = { b: "B", txt: "<=5% der Uferlinie" };
						}
						if (T332_2 > 2) {
							result = { b: "C", txt: ">5% der Uferlinie" };
						}
					}
				}
			}
		}
		this.result = result;
	}
}

function BewertungFliessgewaesser_3_4_1() {

	Bewertung.call(this);

	this.nr = "3.4.1";
	this.txt = "Beeinträchtigung der Durchgängigkeit (bezogen auf das gesamte nachfolgende Fließgewässer)";
	this.lrtCodes = [3260, 3270];

	this.bewerte = function (b) {
		var result = -1;

		var T341 = b.getValue("T341");
		if ([3260, 3270].indexOf(b.lrtCode) >= 0) {
			if (T341 > 0) {
				result = 0;
				if (T341 === 1) {
					result = { b: "A", txt: "keine, Durchgängigkeit vorhanden" };
				}
				if (T341 === 2) {
					result = { b: "B", txt: "wenig, Durchgängigkeit teilweise vorhanden" };
				}
				if (T341 === 3) {
					result = { b: "C", txt: "stark, Durchgängigkeit nicht vorhanden" };
				}
			}
		}
		this.result = result;
	}
}

/* ----------------- */

function BewertungStillgewaesser_1_1_1() {

	Bewertung.call(this);

	this.nr = "1.1.1";
	this.txt = "Deckungsgrad des aktuell besiedelbaren Gewässergrundes mit lr-typischer Vegetation";
	this.lrtCodes = [3110, 3131, 3140, 3160];

	this.bewerte = function (b) {
		// console.info("BewertungStillgewaesser_1_1_1");
		// Deckungsgrad des aktuell besiedelbaren Gewässergrundes mit lr-typischer Vegetation 3110, 3131, 3132, 3140, 3160
		let result = -1;
		let lrtCode = b.lrtCode;
		let T111_1 = Number.parseInt(b.getValue("t111_1"));
		let T111_2 = b.getValue("T111_2");
		// 1       2       3        4      5
		// >50%    >25%    >=10%    >5%    <5%

		if (Number.isInteger(T111_2) && T111_2 > 0) {
			let nrEingabe = Number.isInteger(T111_1);
			let isValid = !nrEingabe;
			if (nrEingabe) {
				isValid = T111_2 === 5 && T111_1 < 5 ||
					T111_2 === 4 && T111_1 >= 5 && T111_1 < 10 ||
					T111_2 === 3 && T111_1 >= 10 && T111_1 <= 25 ||
					T111_2 === 2 && T111_1 > 25 && T111_1 <= 50 ||
					T111_2 === 1 && T111_1 > 50;
				result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
			}
			if (isValid) {
				if (lrtCode === 3110 || lrtCode === 3131 || lrtCode === 3140) {
					if (T111_2 === 1) {
						result = { b: 'A', txt: "> 50%" };
					}
					if (T111_2 === 2 || T111_2 === 3) {
						result = { b: 'B', txt: "10 - 50%" };
					}
					if (T111_2 === 4 || T111_2 === 5) {
						result = { b: 'C', txt: "< 10%" };
					}
				}
				if (lrtCode === 3160) {
					if (T111_2 === 1 || T111_2 === 2) {
						result = { b: 'A', txt: "<= 25%" };
					}
					if (T111_2 === 3) {
						result = { b: 'B', txt: "=> 10%" };
					}
					if (T111_2 === 4 || T111_2 === 5) {
						result = { b: 'C', txt: "< 10%" };
					}
				}
			}
		}
		this.result = result;
	}
};

function BewertungStillgewaesser_1_1_2() {

	Bewertung.call(this);

	this.nr = "1.1.2";
	this.txt = "Deckungsgrad des aktuell besiedelbaren Uferbereiches mit lebensraumtypischer Vegetation";
	this.lrtCodes = [3131, 3132];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T112_1 = Number.parseInt(b.getValue("T112_1"));
		let T112_2 = b.getValue("T112_2");

		// 1       2       3        4      5
		// >50%    >25%    >=10%    >5%    <5%

		if (Number.isInteger(T112_2) && T112_2 > 0) {
			let nrEingabe = Number.isInteger(T112_1);
			let isValid = !nrEingabe;
			if (nrEingabe) {
				isValid = T112_2 === 5 && T112_1 < 5 ||
					T112_2 === 4 && T112_1 >= 5 && T112_1 < 10 ||
					T112_2 === 3 && T112_1 >= 10 && T112_1 <= 25 ||
					T112_2 === 2 && T112_1 > 25 && T112_1 <= 50 ||
					T112_2 === 1 && T112_1 > 50;
				result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
			}
			if (isValid) {
				if (lrtCode === 3131 || lrtCode === 3132) {
					if (T112_2 === 1) {
						result = { b: 'A', txt: "> 50%" };
					} else if (T112_2 === 2 || T112_2 === 3) {
						result = { b: 'B', txt: "10 - 50%" };
					} else if (T112_2 === 4 || T112_2 === 5) {
						result = { b: 'C', txt: "< 10%" };
					}
				}
			}
		}
		this.result = result;
	};
};


function BewertungStillgewaesser_1_1_3() {

	Bewertung.call(this);

	this.nr = "1.1.3";
	this.txt = "Aquatische Vegetation mit Grundrasen, Tauchfluren, Schwimmblattfluren, Schwebematten, Schwimmdecken und Strandlings-/Zwergbinsen-Uferfluren";
	// this.lrtCodes=[3131, 3132, 3150, 3160];
	this.lrtCodes = [3150];

	/*
	this.berechne=function(b) {
		let T113_1 = b.getValue("T113_1");
		this.T113_2 = T113_1.length;
	}
	*/

	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		var UC1 = b.getValue("UC1");
		if (lrtCode === 3150 && ["UST", "USP"].indexOf(UC1) >= 0) {
			document.getElementById("value_144_t113_1_6_" + b.datensatzNr).style.display = 'none';
			document.getElementById("name_144_t113_1_6_" + b.datensatzNr).style.display = 'none';
		}
		else {
			document.getElementById("value_144_t113_1_6_" + b.datensatzNr).style.display = '';
			document.getElementById("name_144_t113_1_6_" + b.datensatzNr).style.display = '';
		}
	};

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		let T113_1 = b.getValue("T113_1");
		var T113_2 = T113_1.length;
		var UC1 = b.getValue("UC1");

		// console.info("uc1="+UC1);
		if (lrtCode === 3150) {
			if (["UST", "USP"].indexOf(UC1) >= 0) {

				if (T113_2 >= 2) {
					result = { b: 'A', txt: "Temporäre Kleingewässer, Torfstiche: >= 2 Elemente" };

				} else if (T113_2 === 1) {
					result = { b: 'B', txt: "Temporäre Kleingewässer, Torfstiche: = 1 Element" };
				}
				else {
					// result = {b:'-2', txt:"Temporäre Kleingewässer, Torfstiche: = 1 Element"};
					result = { b: '0' };
				}
			}
			else if (["USG", "USC", "USA", "USS", "USL", "USB", "USK", "USW"].indexOf(UC1) >= 0) {
				if (T113_2 >= 3) {
					result = { b: 'A', txt: "Seen, Teiche, Altwasser, Abgrabungsgewässer, Permanente Kleingewässer >= 3 Elemente" };
				}
				if (T113_2 === 2) {
					result = { b: 'B', txt: "Seen, Teiche, Altwasser, Abgrabungsgewässer, Permanente" };
				}
				if (T113_2 === 1) {
					result = { b: 'C', txt: "Seen, Teiche, Altwasser, Abgrabungsgewässer, Permanente Kleingewässer = 1 Element" };
				}
			} else {
				result = { b: 0, err: "Wenn der Überlagerungscode 1 nicht USG,USC,USA,USS,USL,USB,USK,USW,UST,USP ist, kann die Bewertung nicht vorgenommen werden." };
			}
		}
		this.result = result;
	};
};

function BewertungStillgewaesser_1_2_1() {

	Bewertung.call(this);

	this.nr = "1.2.1";
	this.txt = "Ufer- bzw. Verlandungsvegetation";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.berechne = function (b) {
		var lrtCode = b.lrtCode;
		var T121_1 = b.getValue("T121_1");
		var T121_2 = 0;

		if ([3110, 3131, 3132].indexOf(lrtCode) >= 0) {
			if (T121_1.indexOf(5) >= 0) T121_2 += 1;
			if (T121_1.indexOf(6) >= 0) T121_2 += 1;
			if (T121_1.indexOf(7) >= 0) T121_2 += 1;
			if (T121_1.indexOf(8) >= 0) T121_2 += 1;
			if (T121_1.indexOf(11) >= 0) T121_2 += 1;
		}
		if (lrtCode === 3140) {
			/*
			if (T121_1.indexOf(5)>=0) T121_2+=1;
			if (T121_1.indexOf(6)>=0) T121_2+=1;
			if (T121_1.indexOf(7)>=0) T121_2+=1;
			if (T121_1.indexOf(8)>=0) T121_2+=1;
			if (T121_1.indexOf(9)>=0) T121_2+=1;
			if (T121_1.indexOf(10)>=0) T121_2+=1;
			if (T121_1.indexOf(13)>=0) T121_2+=1;
			*/
			if (T121_1.indexOf(5) >= 0) T121_2 += 1;
			if (T121_1.indexOf(6) >= 0) T121_2 += 1;
			if (T121_1.indexOf(7) >= 0) T121_2 += 1;
			if (T121_1.indexOf(8) >= 0) T121_2 += 1;
			if (T121_1.indexOf(9) >= 0) T121_2 += 1;
			if (T121_1.indexOf(10) >= 0) T121_2 += 1;
			if (T121_1.indexOf(11) >= 0) T121_2 += 1;
			if (T121_1.indexOf(12) >= 0) T121_2 += 1;
		}
		if (lrtCode === 3150) {
			if (T121_1.indexOf(5) >= 0) T121_2 += 1;
			if (T121_1.indexOf(6) >= 0) T121_2 += 1;
			if (T121_1.indexOf(7) >= 0) T121_2 += 1;
			if (T121_1.indexOf(9) >= 0) T121_2 += 1;
			if (T121_1.indexOf(10) >= 0) T121_2 += 1;
			if (T121_1.indexOf(11) >= 0) T121_2 += 1;
			if (T121_1.indexOf(12) >= 0) T121_2 += 1;
		}
		if (lrtCode === 3160) {
			if (T121_1.indexOf(1) >= 0) T121_2 += 1;
			if (T121_1.indexOf(2) >= 0) T121_2 += 1;
			if (T121_1.indexOf(3) >= 0) T121_2 += 1;
			if (T121_1.indexOf(8) >= 0) T121_2 += 1;
			if (T121_1.indexOf(7) >= 0) T121_2 += 1;
			if (T121_1.indexOf(5) >= 0) T121_2 += 1;
			if (T121_1.indexOf(6) >= 0) T121_2 += 1;
			if (T121_1.indexOf(9) >= 0) T121_2 += 1;
			if (T121_1.indexOf(10) >= 0) T121_2 += 1;
			if (T121_1.indexOf(11) >= 0) T121_2 += 1;
			if (T121_1.indexOf(12) >= 0) T121_2 += 1;
		}
		var targetId = getTextfieldId(b.layerId, b.datensatzNr, "t121_2");
		document.getElementById(targetId).value = T121_2;
	}
	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T121_2 = parseInt(b.getValue("T121_2"));

		if ([3110, 3131, 3132].indexOf(lrtCode) >= 0) {
			if (T121_2 >= 3) {
				result = { b: 'A', txt: ">= 3 typisch ausgebildete Elemente" };
			}
			if (T121_2 === 2) {
				result = { b: 'B', txt: "2 typisch ausgebildete Elemente" };
			}
			if (T121_2 === 1) {
				result = { b: 'C', txt: "1 typisch ausgebildetes Element" };
			}
		}
		if ([3150].indexOf(lrtCode) >= 0) {
			if (T121_2 >= 4) {
				result = { b: 'A', txt: ">= 4 typisch ausgebildete Elemente" };
			}
			if (T121_2 === 3) {
				result = { b: 'B', txt: "3 typisch ausgebildete Elemente" };
			}
			if (T121_2 <= 2) {
				result = { b: 'C', txt: "2 typisch ausgebildete Elemente" };
			}
		}
		if ([3140, 3160].indexOf(lrtCode) >= 0) {
			if (T121_2 >= 5) {
				result = { b: 'A', txt: ">= 5 typisch ausgebildete Elemente" };
			}
			if (T121_2 === 4) {
				result = { b: 'B', txt: "4 typisch ausgebildete Elemente" };
			}
			if (T121_2 <= 3) {
				result = { b: 'C', txt: "3 typisch ausgebildete Elemente" };
			}
		}
		this.result = result;
	};
};



function BewertungStillgewaesser_2_1_1() {

	Bewertung.call(this);

	this.nr = "2.1.1";
	this.txt = "Gesamtanzahl lr-typischer und Anzahl besonders charakteristischer Pflanzenarten";
	this.lrtCodes = [3110, 3131, 3132, 3150, 3160];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T211_1_1 = parseInt(b.getValue("T211_1_1"));
		var T211_1_2 = parseInt(b.getValue("T211_1_2"));
		var UC1 = b.getValue("UC1");

		if (isNaN(T211_1_1) || T211_1_1 < 0) {
			return result;
		}

		if ([3110].indexOf(lrtCode) >= 0) {
			if (T211_1_1 >= 4 && T211_1_2 >= 2) {
				result = { b: 'A', txt: "" };
			}
			else if (T211_1_1 >= 2 && T211_1_2 >= 1) {
				result = { b: 'B', txt: "" };
			}
			else if (T211_1_1 >= 1 && T211_1_2 >= 1) {
				result = { b: 'C', txt: "" };
			}
		}
		if ([3131].indexOf(lrtCode) >= 0) {
			if (!isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'A', txt: "" };
				} else if (T211_1_1 >= 3 && T211_1_2 >= 1) {
					result = { b: 'B', txt: "" };
				} else if (T211_1_1 >= 1 && T211_1_2 >= 1) {
					result = { b: 'C', txt: "" };
				}
			}
		}
		if ([3132].indexOf(lrtCode) >= 0) {
			if (T211_1_1 > 8) {
				result = { b: 'A', txt: "" };
			} else if (T211_1_1 >= 4) {
				result = { b: 'B', txt: "" };
			} else {
				result = { b: 'C', txt: "" };
			}
		}
		if ([3150].indexOf(lrtCode) >= 0) {

			if (["USG", "USC", "USA", "USS", "USL", "USB", "USK", "USW", "UST", "USP"].indexOf(UC1) >= 0) {
				if (
					(UC1 === "USG" && T211_1_1 >= 10) ||
					(UC1 === "USC" && T211_1_1 >= 8) ||
					(["USA", "USS", "USL", "USB", "USK"].indexOf(UC1) >= 0 && T211_1_1 >= 7) ||
					(["USW", "UST"].indexOf(UC1) >= 0 && T211_1_1 >= 5) ||
					(UC1 === "USP" && T211_1_1 >= 3)
				) {
					result = { b: 'A', txt: "" };
				}
				else if (
					(UC1 === "USG" && T211_1_1 >= 5 && T211_1_1 < 10) ||
					(UC1 === "USC" && T211_1_1 >= 5 && T211_1_1 <= 7) ||
					(["USA", "USS", "USL", "USB", "USK"].indexOf(UC1) >= 0 && T211_1_1 > 4 && T211_1_1 <= 6) ||
					(["USW", "UST"].indexOf(UC1) >= 0 && T211_1_1 === 4) ||
					(UC1 === "USP" && T211_1_1 === 2)
				) {
					result = { b: 'B', txt: "" };
				}
				else if (
					(UC1 === "USG" && T211_1_1 <= 4) ||
					(UC1 === "USC" && T211_1_1 <= 4) ||
					(["USA", "USS", "USL", "USB", "USK"].indexOf(UC1) >= 0 && T211_1_1 <= 3) ||
					(["USW", "UST"].indexOf(UC1) >= 0 && T211_1_1 <= 3) ||
					(UC1 === "USP" && T211_1_1 === 1)
				) {
					result = { b: 'C', txt: "" };
				}
			}
			else {
				result = { b: 0, err: "Wenn der Überlagerungscode 1 nicht USG,USC,USA,USS,USL,USB,USK,USW,UST,USP ist, kann die Bewertung nicht vorgenommen werden." };
			}
		}
		if ([3160].indexOf(lrtCode) >= 0) {
			if (!isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_2 >= 9) {
					result = { b: 'A', txt: "" };
				} else if (T211_1_2 >= 3) {
					result = { b: 'B', txt: "" };
				} else {
					result = { b: 'C', txt: "" };
				}
			}
		}
		this.result = result;
	}

}

function BewertungStillgewaesser_2_1_1_a() {

	Bewertung.call(this);

	this.nr = "2.1.1a";
	this.txt = "Inventar Kennarten: Isoetes lacustris, Littorella uniflora, Lobelia dortmanna";
	this.lrtCodes = [];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T211A = b.getValue("T211A");
		var UC1 = b.getValue("UC1");
		if (lrtCode === 3110) {
			if (T211A >= 2) {
				result = { b: 'A', txt: "Anzahl lr-typische Arten >= 2" };
			}
			// B T211A = 1 mit "z" Anzahl lr-typische Arten 1 mit „z“
			// C T211A = 1 mit "v" Anzahl lr-typische Arten 1 mit „v“
		}

		return { b: "unklar", txt: "Bewertungskriterium unklar" };
	}
}


function BewertungStillgewaesser_2_1_2() {

	Bewertung.call(this);

	this.nr = "2.1.2";
	this.txt = "Anzahl besonders charakteristischer Pflanzenarten und Armleuchteralgen";
	this.lrtCodes = [3140];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T212_2 = parseInt(b.getValue("T212_2"));
		let UC1 = b.getValue("UC1");

		if (lrtCode === 3140) {
			if (
				(UC1 === "USG" && T212_2 >= 10) ||
				(["USW", "USS", "USL", "USB", "USK", "USC"].indexOf(UC1) >= 0 && T212_2 >= 4) ||
				(UC1 === "UST" && T212_2 >= 3) ||
				(UC1 === "USP" && T212_2 >= 2)
			) {
				result = { b: 'A', txt: "" };
			}
			else if (
				(UC1 === "USG" && T212_2 >= 5) ||
				(["USW", "USS", "USL", "USB", "USK", "USC"].indexOf(UC1) >= 0 && T212_2 >= 3) ||
				(UC1 === "UST" && T212_2 === 2) ||
				(UC1 === "USP" && T212_2 === 1)
			) {
				result = { b: 'B', txt: "" };
			}
			else if (
				(UC1 === "USG" && T212_2 < 5) ||
				(["USW", "USS", "USL", "USB", "USK", "USC"].indexOf(UC1) >= 0 && T212_2 < 3) ||
				(UC1 === "UST" && T212_2 < 2) ||
				(UC1 === "USP" && T212_2 < 1)
			) {
				result = { b: 'C', txt: "" };
			}
		}
		this.result = result;
	}
}

function BewertungStillgewaesser_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Tierarten";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}

	this.berechneKorrekturWert = function (b) {
		var T22_1 = b.getValue("T22");
		var T22_2 = b.getTextValue("T22_2");
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
	}
}

function BewertungStillgewaesser_2_2_a() {

	Bewertung.call(this);

	this.nr = "2.2a";
	this.txt = "Bodenständige, typische Libellenarten";
	this.lrtCodes = [3160];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T22A = parseInt(b.getValue("T22A"));

		if (lrtCode === 3160) {
			if (Number.isInteger(T22A)) {
				if (T22A >= 5) {
					result = { b: 'A' };
				} else if (T22A === 3 || T22A === 4) {
					result = { b: 'B' };
				} else if (T22A <= 2) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungStillgewaesser_3_1_1() {

	Bewertung.call(this);

	this.nr = "3.1.1";

	this.varA = new BewertungStillgewaesser_3_1_1_a();
	this.varB = new BewertungStillgewaesser_3_1_1_b();

	this.prepareHTML = function (b) {
		this.isSeeGr50ha = b.seeGr50ha();
		if (this.isSeeGr50ha) {
			console.info("BewertungStillgewaesser_3_1_1.prepareHTML Variante A");
			this.showVarA(b);
		} else {
			console.info("BewertungStillgewaesser_3_1_1.prepareHTML Variante B");
			this.showVarB(b);
		}
		const chBox = document.getElementById(b.layerId + "_see_gr_50ha_0");
		chBox.addEventListener("change", () => {
			if (chBox.checked) {
				this.isSeeGr50ha = true;
				this.showVarA(b);
			} else {
				this.isSeeGr50ha = false;
				this.showVarB(b);
			}
		});
	};

	this.showVarA = function (b) {
		console.info("BewertungStillgewaesser_3_1_1.showVarA");
		b.setVisibiltyOfBlock("3.1.1 a", true);
		b.setVisibiltyOfBlock("3.1.1 b", false);
		this.bewerte = this.varA.bewerte;
		this.berechneKorrekturWert = null;
		this.getKorrekturWert = null;
	};
	this.showVarB = function (b) {
		console.info("BewertungStillgewaesser_3_1_1.showVarB");
		b.setVisibiltyOfBlock("3.1.1 b", true);
		b.setVisibiltyOfBlock("3.1.1 a", false);
		this.bewerte = null;
		this.berechneKorrekturWert = this.varB.berechneKorrekturWert;
		this.getKorrekturWert = this.varB.getKorrekturWert;
	};

	/*
	this.bewerte = function (b) {
		if (b.seeGr50ha()) {
			this.varA.bewerte(b);
		}
	}

	this.berechneKorrekturWert = function (b) {
		if (!b.seeGr50ha()) {
			this.varB.berechneKorrekturWert(b);
		}
	}

	this.getKorrekturWert = function (b) {
		if (this.isSeeGr50ha) {
			return this.varB.korrekturWert;
		} else {
			return 0;
		}
	}
	*/

	this.isRequired = function (b) {
		const isRequired = this.varA.isRequired(b) || this.varB.isRequired(b);
		console.info("!!!!!!!!!isReq = " + isRequired);
		console.info("isRequired " + this.nr + "required=" + isRequired);
		return isRequired;
	}

}

function BewertungStillgewaesser_3_1_1_a() {
	Bewertung.call(this);

	this.nr = "3.1.1a";
	this.txt = "Seeuferstruktur-Index nach WRRL (nur für Stillgewässer >=50 ha";
	this.lrtCodes = [3131, 3132, 3140, 3150];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T311A_2 = b.getValue("T311A_2");

		if ([3131, 3132, 3140, 3150].indexOf(lrtCode) >= 0) {
			if (T311A_2 === 1 || T311A_2 === 2) {
				result = { b: 'A', txt: "" };
			}
			if (T311A_2 === 3) {
				result = { b: 'B', txt: "" };
			}
			if (T311A_2 === 4 || T311A_2 === 5) {
				result = { b: 'C', txt: "" };
			}
		}
		this.result = result;
	}

	this.isRequired = function (b) {
		return this.lrtCodes.indexOf(b.lrtCode) >= 0 && b.seeGr50ha();
	}
}

function BewertungStillgewaesser_3_1_1_b() {

	Bewertung.call(this);

	this.nr = "3.1.1b";
	this.txt = "Strukturen zur Stoffeintragsminderung (nur für Stillgewässer < 50 ha)";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}

	this.berechneKorrekturWert = function (b) {
		var T311B_2 = b.getValue("T311B_2");
		this.korrekturWert = T311B_2 < 3 ? 0 : -1;
	};

	this.isRequired = function (b) {
		return this.lrtCodes.indexOf(b.lrtCode) >= 0 && !b.seeGr50ha();
	}
}

function BewertungStillgewaesser_3_1_2() {

	Bewertung.call(this);

	this.nr = "3.1.2";
	this.txt = "Punktuelle Beeinträchtigung von Wasserkörper und Ufer (Abwassereinleitung)";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T312_1 = b.getValue("T312_1");
		if ([3110, 3160].indexOf(lrtCode) >= 0) {
			if (T312_1 === 1) {
				result = { b: 'A', txt: "keine Abwassereinleitungen" };
			}
			if ([2, 3, 4, 5, 6, 7, 8].indexOf(T312_1) >= 0) {
				result = { b: 'C', txt: "signifikant (nach Aussehen und Geruch Abwasser)" };
			}
		}

		if ([3131, 3132].indexOf(lrtCode) >= 0) {
			if (b.seeGr50ha()) {
				if (T312_1 === 1 || T312_1 === 2) {
					result = { b: 'A', txt: "keine Abwassereinleitungen, ausgenommen einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
				}
				if ([3, 4, 5, 6, 7, 8].indexOf(T312_1) >= 0) {
					result = { b: 'C', txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
				}
			} else {
				if (T312_1 === 1) {
					result = { b: 'A', txt: "keine Abwassereinleitungen, ausgenommen einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
				}
				if ([2, 3, 4, 5, 6, 7, 8].indexOf(T312_1) >= 0) {
					result = { b: 'C', txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
				}
			}
		}

		if ([3140, 3150].indexOf(lrtCode) >= 0) {
			if (T312_1 === 1 || T312_1 === 2) {
				result = { b: 'A', txt: "keine Abwassereinleitungen, ausgenommen einzelne Kleineinleitungen (< 8 m3/d oder < 50 EW)" };
			}
			if ([3, 4, 5, 6, 7, 8].indexOf(T312_1) >= 0) {
				result = { b: 'C', txt: "signifikante Abwassereinleitung vorhanden (Aussehen und Geruch der Einleitung weisen auf Abwasser hin)" };
			}
		}
		this.result = result;
	}

}


function BewertungStillgewaesser_3_1_3() {

	Bewertung.call(this);

	this.nr = "3.1.3";
	this.txt = "Untere Makrophytengrenze (nur für tiefe Stillgewässer und >= 2ha Wasserfläche";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];


	this.bewerte = function (b) {
		var result = -1,
			lrtCode = b.lrtCode,
			T313_1 = b.getValue("T313"),
			T313_4 = parseInt(b.getValue("T313_4")),
			T313_2 = parseFloat(b.getValue("T313_2")),
			T313_3 = parseInt(b.getValue("T313_3")),
			flaeche = b.getFlaecheInHa(b.layerId, b.datensatzNr);

		if (T313_1 || flaeche < 2) {
			this.result = { skiped: true };
			if (flaeche < 2) {
				this.result.msg = "Fläche kleiner als 2 ha."
			}
			return;
		}

		if ([3110, 3160].indexOf(lrtCode) >= 0) {
			if (T313_4 === 1) {
				// sauer
				if (T313_2 > 8) {
					result = { b: 'A', txt: "sauer: > 8m" };
				}
				if (T313_2 >= 4 && T313_2 <= 8) {
					result = { b: 'B', txt: "sauer: 4 - 8m" };
				}
				if (T313_2 < 4) {
					result = { b: 'C', txt: "sauer: < 4m" };
				}
			}
			if (T313_4 === 2) {
				if (T313_2 > 5) {
					result = { b: 'A', txt: "subneutral: > 5m" };
				}
				if (T313_2 >= 3 && T313_2 <= 5) {
					result = { b: 'B', txt: "subneutral: 3 - 5m" };
				}
				if (T313_2 < 3) {
					result = { b: 'C', txt: "subneutral: < 3m" };
				}
			}
		}
		if ([3131, 3132].indexOf(lrtCode) >= 0) {
			if (T313_2 > 5) {
				result = { b: 'A', txt: "> 5 m" };
			}
			if (T313_2 >= 3 && T313_2 <= 5) {
				result = { b: 'B', txt: "3 – 5 m" };
			}
			if (T313_2 < 3) {
				result = { b: 'C', txt: "< 3m" };
			}
		}
		if (3140 === lrtCode) {
			if (T313_3 === 1 && T313_2 > 10) {
				result = { b: 'A', txt: "oligotroph: > 10m" };
			}
			if (T313_3 === 2 && T313_2 > 6.5) {
				result = { b: 'A', txt: "mesotroph: > 6,5m" };
			}
			if (T313_3 === 1 && T313_2 >= 6.5 && T313_2 <= 10) {
				result = { b: 'B', txt: "oligotroph: 6,5 – 10m" };
			}
			if (T313_3 === 2 && T313_2 >= 4 && T313_2 <= 6.5) {
				result = { b: 'B', txt: "mesotroph: >= 4 - 6,5m" };
			}

			if (T313_3 === 1 && T313_2 < 6.5) {
				result = { b: 'C', txt: "oligotroph: <6,5" };
			}
			if (T313_3 === 2 && T313_2 < 4) {
				result = { b: 'C', txt: "mesotroph: < 4" };
			}
		}
		if (3150 === lrtCode) {
			if (T313_2 > 2.5) {
				result = { b: 'A', txt: "> 2,5 m" };
			}
			if (T313_2 >= 1.5 && T313_2 <= 2.5) {
				result = { b: 'B', txt: "1,5 – 2,5m" };
			}
			if (T313_2 < 1.5) {
				result = { b: 'C', txt: "< 1,5m" };
			}
		}

		this.result = result;
	}

}

function BewertungStillgewaesser_3_1_5() {

	Bewertung.call(this);

	this.nr = "3.1.5";
	this.txt = "Deckung Störzeiger / Hypertrophierungszeiger";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.prepareHTML = function (b) {
		let fields = ["T315_1", "T315_2", "T315_3"];
		for (let i = 0, count = fields.length; i < count; i++) {
			let k = fields[i].toLowerCase();
			let elements = b.map[k];
			if (elements && elements.length > 0) {
				elements[0].title += " - bei keinen Arten bitte 0 eintragen";
			}
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T315_1 = parseInt(b.getValue("T315_1"));
		var T315_2 = parseInt(b.getValue("T315_2"));
		var T315_3 = parseInt(b.getValue("T315_3"));
		if ([3110, 3131, 3132, 3140, 3150, 3160].indexOf(lrtCode) >= 0) {
			if (!isNaN(T315_1) && !isNaN(T315_2) && !isNaN(T315_3)) {
				result = 0;
				if (T315_1 <= 2 && T315_2 === 0 && T315_3 === 0) {
					result = { b: 'A', txt: "Anzahl bis 2x „vereinzelt (v)“" };
				} else if (T315_2 >= 3 || T315_3 >= 1) {
					result = { b: 'C', txt: "Anz. ab 3x „zahlreich (z)“ oder Anz. ab 1x „dominant (d)“" };
				} else {
					result = { b: 'B', txt: "Anz. bis 2x „zahlreich (z)“ oder Anz. ab 3x „vereinzelt (v)“" };
				}
			}
		}
		this.result = result;
	}
}


function BewertungStillgewaesser_3_1_7() {

	Bewertung.call(this);

	this.nr = "3.1.7";
	this.txt = "Veränderung des Wasserregimes";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.berechne = function (b) {
		var T317_1 = b.getBoolValue("T317_1_1");
		if (T317_1 === -1 || !T317_1) {
			b.disable("T317_2_1", true);
		}
		else {
			b.disable("T317_2_1", false);
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T317_1 = b.getBoolValue("T317_1_1");
		var T317_2 = b.getBoolValue("T317_2_1");

		if ([3110, 3131, 3132, 3140, 3150, 3160].indexOf(lrtCode) >= 0) {
			if (T317_1 !== -1) {

				if (T317_1) {
					if (T317_2 != -1) {
						if (T317_2) {
							result = { b: 'C', txt: "Veränderung vorhanden, aber mit Funktion" };
						}
						else {
							result = { b: 'A', txt: "keine Veränderung oder Veränderung vorhanden, aber ohne Funktion" };
						}
					}
				}
				else {
					result = { b: 'A', txt: "keine Veränderung oder Veränderung vorhanden, aber ohne Funktion" };
				}
			}
		}
		this.result = result;
	}
}

function BewertungStillgewaesser_3_2_1() {

	Bewertung.call(this);

	this.nr = "3.2.1";
	this.txt = "Vollständigkeit des naturnahen Ufersaumes (nur für Stillgewässer < 50 ha)";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T321_1 = Number.parseInt(b.getValue("T321_1"));
		let T321_2 = parseInt(b.getValue("T321_2"));

		if (!b.seeGr50ha()) {
			if ([3110, 3131, 3132, 3140, 3150, 3160].indexOf(lrtCode) >= 0) {
				// 1       2        3        4        5
				// 100%    >=95%    >=75%    >=50%    <50%    
				if (Number.isInteger(T321_2) && T321_2 > 0) {
					let nrEingabe = Number.isInteger(T321_1);
					let isValid = !nrEingabe;
					if (nrEingabe) {
						isValid = T321_2 === 5 && T321_1 < 50 ||
							T321_2 === 4 && T321_1 >= 50 && T321_1 < 75 ||
							T321_2 === 3 && T321_1 >= 75 && T321_1 < 95 ||
							T321_2 === 2 && T321_1 >= 95 && T321_1 < 100 ||
							T321_2 === 1 && T321_1 === 100;
						result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
					}
					if (isValid) {
						if (T321_2 === 1 || T321_2 === 2) {
							result = { b: 'A', txt: "Seegrösse < 50 ha und >= 90%" };
						}
						if (T321_2 === 3) {
							result = { b: 'B', txt: "Seegrösse < 50 ha und > 75%" };
						}
						if (T321_2 === 4 || T321_2 === 5) {
							result = { b: 'C', txt: "Seegrösse < 50 ha und >= 50%" };
						}
					}
				}
			}
		}
		this.result = result;
	}
	this.isRequired = function (b) {
		return this.lrtCodes.indexOf(b.lrtCode) >= 0 && !b.seeGr50ha();
	}
}

function BewertungStillgewaesser_3_2_2() {

	Bewertung.call(this);

	this.nr = "3.2.2";
	this.txt = "Ablagerung und Verfüllung (nur für Stillgewässer < 1 ha Wasserfläche)";
	this.lrtCodes = [3110, 3131, 3132, 3140, 3150, 3160];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T322_1 = Number.parseInt(b.getValue("T322_1"));
		let T322_2 = parseInt(b.getValue("T322_2"));

		var flaeche = b.getFlaecheInHa(b.layerId, b.datensatzNr);
		if (flaeche < 1) {
			if ([3110, 3131, 3132, 3140, 3150, 3160].indexOf(lrtCode) >= 0) {
				if (Number.isInteger(T322_2) && T322_2 > 0) {
					// 1        2       3       4       5
					// keine    <25%    >25%    >50%    >75%    
					let nrEingabe = Number.isInteger(T322_1);
					let isValid = !nrEingabe;
					if (nrEingabe) {
						isValid = T322_2 === 5 && T322_1 > 75 ||
							T322_2 === 4 && T322_1 <= 75 && T322_1 > 50 ||
							T322_2 === 3 && T322_1 <= 50 && T322_1 > 25 ||
							T322_2 === 2 && T322_1 <= 25 && T322_1 > 0 ||
							T322_2 === 1 && T322_1 === 0;
						result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
					}
					if (isValid) {
						if (T322_2 === 1) {
							result = { b: 'A', txt: "keine bis < 25%" };
						} else if (T322_2 === 2) {
							result = { b: 'B', txt: "> 25%" };
						} else if (T322_2 === 3 || T322_2 === 4 || T322_2 === 5) {
							result = { b: 'C', txt: "> 50% und < 75%" };
						}
					}
				}
			}
		}
		this.result = result;
	}

	this.isRequired = function (b) {
		var flaeche = b.getFlaecheInHa(b.layerId, b.datensatzNr);
		return this.lrtCodes.indexOf(b.lrtCode) >= 0 && flaeche < 1;
	}
}


// Offenland
function BewertungOffenland_1_1_1() {

	Bewertung.call(this);

	this.nr = "1.1.1";
	this.txt = "Deckung lebensraumtypischer Vegetation";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T111_1 = b.getValue("T111_1");

		if ([1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510].indexOf(lrtCode) >= 0) {
			if (!isNaN(T111_1) && T111_1 > 0) {
				result = 0;
				if (T111_1 === 1 || T111_1 === 2) {
					result = { b: 'A', txt: "> 90 % der Fläche" };
				}
				if (T111_1 === 3) {
					result = { b: 'B', txt: "> 75% der Fläche" };
				}
				if (T111_1 === 4) {
					result = { b: 'C', txt: "> 50% der Fläche" };
				}
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_1_1_2() {

	Bewertung.call(this);

	this.nr = "1.1.2";
	this.txt = "Deckung lebensraumtypischer Zwergsträucher";
	this.lrtCodes = [2310, 4010, 4030];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T112_1 = b.getValue("T112_1");

		if ([2310, 4010, 4030].indexOf(lrtCode) >= 0) {
			if (!isNaN(T112_1) && T112_1 > 0) {
				result = 0;
				if (T112_1 === 1 || T112_1 === 2) {
					result = { b: 'A', txt: "> 90 % der Fläche" };
				}
				if (T112_1 === 3) {
					result = { b: 'B', txt: "> 75% der Fläche" };
				}
				if (T112_1 === 4) {
					result = { b: 'C', txt: "> 50% der Fläche" };
				}
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_1_1_3() {

	Bewertung.call(this);

	this.nr = "1.1.3";
	this.txt = "Deckung der Kräuter";
	this.lrtCodes = [6410, 6440, 6510];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T211_3_1 = b.getValue("T211_3_1");
		var T113_1 = b.getValue("T113_1");

		if (!isNaN(T113_1) && T113_1 > 0) {
			result = 0;
			if (lrtCode === 6410) {
				if (T211_3_1 === 1 || T211_3_1 === 2) {
					if (T211_3_1 === 1 && [1, 2, 3, 4, 5].indexOf(T113_1) >= 0 || T211_3_1 === 2 && [1, 2, 3].indexOf(T113_1) >= 0) {
						result = { b: 'A' };
					} else if (T211_3_1 === 1 && [5, 6].indexOf(T113_1) >= 0 || T211_3_1 === 2 && [4, 5].indexOf(T113_1) >= 0) {
						result = { b: 'B' };
					} else if (T211_3_1 === 1 && T113_1 === 7 || T211_3_1 === 2 && [6, 7].indexOf(T113_1) >= 0) {
						result = { b: 'C' };
					}
				}
				else {
					result = { b: 0, err: "Zur Bewertung muss unter 2.1.1 basenarm oder basenreich gewählt sein." };
				}
			} else if (lrtCode === 6440) {
				if ([1, 2, 3, 4, 5].indexOf(T113_1) >= 0) {
					result = { b: 'A' };
				} else if ([6].indexOf(T113_1) >= 0) {
					result = { b: 'B' };
				} else if (T113_1 === 7) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 6510) {
				if ([1, 2, 3, 4].indexOf(T113_1) >= 0) {
					result = { b: 'A' };
				} else if ([5].indexOf(T113_1) >= 0) {
					result = { b: 'B' };
				} else if ([6, 7].indexOf(T113_1) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}


function BewertungOffenland_1_2_1() {

	Bewertung.call(this);

	this.nr = "1.2.1";
	this.txt = "Altersstruktur des Heidekrauts (Calluna vulgaris) und Wacholders (Juniperus communis)";
	this.lrtCodes = [2310, 4030, 5130];


	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		let titleElement = b.getSectionTitleElement("T121_1");
		if (lrtCode === 2310 || lrtCode === 4030) {
			titleElement.innerText = "1.2.1 Altersstruktur des Heidekrauts (Calluna vulgaris)";
		} else if (lrtCode === 5130) {
			titleElement.innerText = "1.2.1 Altersstruktur des Wacholders (Juniperus communis)";
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T121_1 = b.getValue("T121_1");
		var T121_2 = b.getValue("T121_2");

		if ([2310, 4030, 5130].indexOf(lrtCode) >= 0) {
			if (!isNaN(T121_1) && T121_1 > 0 && !isNaN(T121_2) && T121_2 > 0) {
				result = 0;
				if (T121_1 === 1 && T121_2 === 3) {
					result = { b: 'A' };
				} else if (T121_1 === 1 && T121_2 == 2) {
					result = { b: 'B' };
				} else if (T121_1 === 2 || T121_1 === 1) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_1_3_1() {

	Bewertung.call(this);

	this.nr = "1.3.1";
	this.txt = "Sohlquellen";
	this.lrtCodes = [1340];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;

		if ([1340].indexOf(lrtCode) >= 0) {
			var t131_1_2 = parseInt(b.getValue("t131_1_2"));
			if (!isNaN(t131_1_2) && t131_1_2 > 0) {
				result = { b: 'A' };
			} else {
				result = { b: 'B' };
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_1_3_2_old() {

	Bewertung.call(this);

	this.nr = "1.3.2";
	this.txt = "Anteil offener Sandstellen bzw. vegetationsfreier Rohböden / Geschiebe / Lesesteine";
	this.lrtCodes = [2310, 2330];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T132_1 = b.getValue("T132_1");
		var T132_2 = b.getValue("T132_2");

		if ([2310, 2330].indexOf(lrtCode) >= 0) {
			if (!isNaN(T132_1) && T132_1 > 0 && !isNaN(T132_2) && T132_2 > 0) {
				result = 0;
				if (T132_1 === 1 && [1, 2, 3].indexOf(T132_2) >= 0) {
					result = { b: 'A' };
				} else if (T132_1 === 2 && T132_2 === 4) {
					result = { b: 'B' };
				} else if ([5, 6].indexOf(T132_2)) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_1_3_2() {

	Bewertung.call(this);

	this.nr = "1.3.2";
	this.txt = "Anteil offener Sandstellen bzw. vegetationsfreier Rohböden / Geschiebe / Lesesteine";
	this.lrtCodes = [2310, 2330];

	this.berechne = function (b) {
		b.setCalculated("T132_1", "Ergibt sich aus den Habitaten.")
		if (b.hasHabitat("DGO")) {
			b.setRadioButton("T132_1", 1);
		} else if (b.hasHabitat("DGV")) {
			b.setRadioButton("T132_1", 2);
		} else {
			b.setRadioButton("T132_1", 3);
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T132_2 = b.getValue("T132_2");

		if ([2310, 2330].indexOf(lrtCode) >= 0) {
			if (!isNaN(T132_2) && T132_2 > 0) {
				result = 0;
				if ([1, 2, 3].indexOf(T132_2) >= 0) {
					result = { b: 'A' };
				} else if (T132_2 === 4) {
					result = { b: 'B' };
				} else if ([5, 6].indexOf(T132_2)) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_2_1_1() {

	Bewertung.call(this);

	this.nr = "2.1.1";
	this.txt = "Gesamtanzahl lr-typischer und Anzahl besonders charakteristischer Pflanzenarten";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T211_1_1 = parseInt(b.getValue("T211_1_1"));
		var T211_1_2 = parseInt(b.getValue("T211_1_2"));
		var T211_1_3 = parseInt(b.getValue("T211_1_3"));
		var T211_3_1 = parseInt(b.getValue("T211_3_1"));


		if (lrtCode === 1340) {
			if (!isNaN(T211_1_1) && T211_1_1 > 0 && !isNaN(T211_1_3) && T211_1_3 > 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_3 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_3 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_3 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2310) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2330) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 4010) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 5 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 3 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_1 >= 1 && T211_1_2 >= 0) {
					result = { b: 'C' }; //Abweichung zur BWA ( >=1 bes. char. Art)
				}
			}
		} else if (lrtCode === 4030) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 5130) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0) {
				result = 0;
				if (T211_1_1 >= 10) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5) {
					result = { b: 'B' };
				} else if (T211_1_1 < 5) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 6120) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 2) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 1) {
					result = { b: 'B' };
				} else if (T211_1_1 < 5 && T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 6210) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_1 >= 1 && T211_1_2 >= 0) {
					result = { b: 'C' }; //Abweichung zur BWA ( >=1 bes. char. Art)
				}
			}
		} else if (lrtCode === 6230) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 4) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 & T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 6240) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 6410) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_3_1 === 1) {
					if (T211_1_1 >= 6 && T211_1_2 >= 3) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 4 && T211_1_2 >= 2) {
						result = { b: 'B' };
					} else if (T211_1_2 >= 1) {
						result = { b: 'C' };
					}
				} else if (T211_3_1 === 2) {
					if (T211_1_1 >= 10 && T211_1_2 >= 5) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 7 && T211_1_2 >= 3) {
						result = { b: 'B' };
					} else if (T211_1_2 >= 1) {
						result = { b: 'C' };
					}
				}
			}
		} else if (lrtCode === 6430) {
			var hc = b.getValue("hc");
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (hc === 'VHS') {
					if (T211_1_1 >= 7 && T211_1_2 >= 2) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 5 && T211_1_2 >= 1) {
						result = { b: 'B' };
					} else if (T211_1_2 >= 1) {
						result = { b: 'C' };
					}
				} else if (hc === 'RHF') {
					if (T211_1_1 >= 5 && T211_1_2 === 1) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 3 && T211_1_2 >= 1) {
						result = { b: 'B' };
					} else if (T211_1_2 >= 1) {
						result = { b: 'C' };
					}
				}
			}
		} else if (lrtCode === 6440) {
			if (!isNaN(T211_1_1) && T211_1_1 >= 0 && !isNaN(T211_1_2) && T211_1_2 >= 0) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 7 && T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 6510) {
			if (!isNaN(T211_1_1) && T211_1_1 > 0) {
				result = 0;
				if (T211_1_1 >= 20) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 12) {
					result = { b: 'B' };
				} else if (T211_1_1 < 12) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_2_1_2() {

	Bewertung.call(this);

	this.nr = "2.1.2";
	this.txt = "Deckung Magerkeitszeiger";
	this.lrtCodes = [6510];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T212_1 = parseInt(b.getValue("T212_1"));

		if (lrtCode === 6510) {
			if (!isNaN(T212_1) && T212_1 > 0) {
				result = 0;
				if (T212_1 === 1) {
					result = { b: 'A' };
				} else if (T212_1 === 2) {
					result = { b: 'B' };
				} else if ([3, 4].indexOf(T212_1) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Tierarten";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {
		var T22_1 = b.getValue("T22");
		var T22_2 = b.getTextValue("T22_2");
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
	}
}

function BewertungOffenland_3_1_1() {

	Bewertung.call(this);

	this.nr = "3.1.1";
	this.txt = "Veränderung des Wasserregimes";
	this.lrtCodes = [1340, 6410, 6440];

	this.berechne = function (b) {
		if (b.hasGefcode('YWO')) {
			b.setRadioButton("T311_1_1", "t");
		}
		else {
			b.setRadioButton("T311_1_1", "f");
		}
		if (b.hasGefcode('YWE') || b.hasGefcode('YWF')) {
			b.setRadioButton("T311_1_2", "t");
		}
		else {
			b.setRadioButton("T311_1_2", "f");
		}
		if (b.hasGefcode('YWZ') || b.hasGefcode('YWI')) {
			b.setRadioButton("T311_1_3", "t");
		}
		else {
			b.setRadioButton("T311_1_3", "f");
		}
		if (b.hasGefcode('YWS')) {
			b.setRadioButton("T311_1_4", "t");
		}
		else {
			b.setRadioButton("T311_1_4", "f");
		}
		if (b.hasGefcode('YWG')) {
			b.setRadioButton("T311_1_5", "t");
		}
		else {
			b.setRadioButton("T311_1_5", "f");
		}
		b.setCalculated("T311_1_1", "Aus Grundbogen - ja, wenn Gefährdungscode YWO");
		b.setCalculated("T311_1_2", "Aus Grundbogen - ja, wenn Gefährdungscode YWE oder YWF");
		b.setCalculated("T311_1_3", "Aus Grundbogen - ja, wenn Gefährdungscode YWZ oder YWI");
		b.setCalculated("T311_1_4", "Aus Grundbogen - ja, wenn Gefährdungscode YWS");
		b.setCalculated("T311_1_5", "Aus Grundbogen - ja, wenn Gefährdungscode YWG");
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T311_1_1 = b.getBoolValue("T311_1_1");
		var T311_1_2 = b.getBoolValue("T311_1_2");
		var T311_1_3 = b.getBoolValue("T311_1_3");
		var T311_1_4 = b.getBoolValue("T311_1_4");
		var T311_1_5 = b.getBoolValue("T311_1_5");


		if ([1340, 6410, 6440].indexOf(lrtCode) >= 0) {
			if (T311_1_1 !== -1 && T311_1_2 !== -1 && T311_1_3 !== -1 && T311_1_4 !== -1 && T311_1_5 !== -1) {
				result = 0;
				if (T311_1_1 || T311_1_2 || T311_1_5) {
					result = { b: 'C' };
					b.setRadioButton("T311_1_6", false);
				} else if (T311_1_3 || T311_1_4) {
					result = { b: 'B' };
					b.setRadioButton("T311_1_6", false);
				} else {
					let T311_1_6 = b.getValue("T311_1_6");
					if (T311_1_6 === true) {
						result = { b: 'A' };
					} else {
						result = { err: 'Bestätigen Sie, dass keine Beeinträchtigungen erkennbar sind oder wählen Sie die Gefährdungen im Grundbogen!' };
					}
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_1() {

	Bewertung.call(this);

	this.nr = "3.2.1";
	this.txt = "Stoffeinträge möglich durch fehlende Pufferstrukturen (außerhalb des LRT)";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6440, 6510];

	this.prepareHTML = function (b) {
		b.setCalculated("T321_2", true);
	}

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {
		let T321_1 = Number.parseInt(b.getValue("T321_1"));
		let T321_2 = b.getValue("T321_2");


		if (Number.isInteger(T321_1) && this.oldValueT321_1 !== T321_1) {
			var rbNr;
			if (T321_1 <= 10) {
				rbNr = 7;
			} else if (T321_1 <= 25) {
				rbNr = 6;
			} else if (T321_1 <= 50) {
				rbNr = 5;
			} else if (T321_1 <= 75) {
				rbNr = 4;
			} else if (T321_1 <= 90) {
				rbNr = 3;
			} else if (T321_1 < 100) {
				rbNr = 2;
			} else if (T321_1 === 100) {
				rbNr = 1;
			}

			this.oldValueT321_1 = T321_1;
			this.oldValueT321_2 = rbNr;
			b.setRadioButton("T321_2", rbNr);
			T321_2 = b.getValue("T321_2");
		}

		this.oldValueT321_2 = T321_2;

		if (Number.isInteger(T321_2) && T321_2 > 0) {
			let nrEingabe = Number.isInteger(T321_1);
			let isValid = !nrEingabe;
			if (nrEingabe) {
				// 1       2       3       4       5       6       7
				// 100%    >90%    >75%    >50%    >25%    >10%    <=10%   
				isValid = T321_2 === 7 && T321_1 <= 10 ||
					T321_2 === 6 && T321_1 > 10 && T321_1 <= 25 ||
					T321_2 === 5 && T321_1 > 25 && T321_1 <= 50 ||
					T321_2 === 4 && T321_1 > 50 && T321_1 <= 75 ||
					T321_2 === 3 && T321_1 > 75 && T321_1 <= 90 ||
					T321_2 === 2 && T321_1 > 90 && T321_1 < 100 ||
					T321_2 === 1 && T321_1 === 100;
				if (!isValid) {
					this.korrekturWert = { err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
				}
			}
			if (isValid) {
				this.korrekturWert = [4, 5, 6, 7].indexOf(T321_2) >= 0 ? -1 : 0;
			}
		} else {
			if (Number.isInteger(T321_1) && T321_1 <= 100 && T321_1 >= 0) {
			}
			else {
				this.korrekturWert = { err: "Eingebener Wert ist zu gross oder zu klein." };
			}
		}
	}
}

function BewertungOffenland_3_2_2() {

	Bewertung.call(this);

	this.nr = "3.2.2";
	this.txt = "Schädigung von Vegetation und Strukturen";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.berechne = function (b) {
		var T322_1 = b.getValue("T322_1");
		var T322_2 = b.getValue("T322_2");
		var T322_3 = b.getValue("T322_3");
		var T322_4 = b.getValue("T322_4");

		if (T322_1 > T322_4 || T322_2 > T322_4 || T322_3 > T322_4) {
			b.setRadioButton("T322_4", null);
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T322_1 = b.getValue("T322_1");
		var T322_2 = b.getValue("T322_2");
		var T322_3 = b.getValue("T322_3");
		var T322_4 = b.getValue("T322_4");




		if ([1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510].indexOf(lrtCode) >= 0) {
			if (!isNaN(T322_4) && T322_4 > 0) {
				result = 0;
				if (T322_4 === 1) {
					result = { b: 'A' };
				} else if (T322_4 === 3) {
					result = { b: 'B' };
				} else if ([4, 5, 6].indexOf(T322_4) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_3() {

	Bewertung.call(this);

	this.nr = "3.2.3";
	this.txt = "Landwirtschaftliche Nutzung / Pflege";
	this.lrtCodes = [1340, 5130, 6120, 6210, 6230, 6240, 6410, 6440, 6510];

	/*
	this.berechne = function (b) {
		var nutzintens_1 = b.getValue("nutzintens_1");
		var nutzintens_2 = b.getValue("nutzintens_2");
		var nutzintens_3 = b.getValue("nutzintens_3");
		var nutzintens_4 = b.getValue("nutzintens_4");

		if (nutzintens_4 === "t" || nutzintens_3 === "t") {
			b.setRadioButton("T323_1", 3);
		} else if (nutzintens_4 === "f" || nutzintens_3 === "f") {
			b.setRadioButton("T323_1", 2);
		} else {
			if (nutzintens_1 === '' && nutzintens_2 === '' && nutzintens_3 === '' && nutzintens_4 === '') {
				b.setRadioButton("T323_1", -1);
			} else {
				b.setRadioButton("T323_1", 1);
			}
		}
		var elements = b.getElements("T323_1");
		b.setCalculated("T323_1", "abgeleitet aus Nutzung im Grundbogen");
	}
	*/

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T323_1 = b.getValue("T323_1");

		if ([1340, 5130, 6120, 6210, 6230, 6240, 6410, 6440, 6510].indexOf(lrtCode) >= 0) {
			if (!isNaN(T323_1) && T323_1 > 0) {
				result = 0;
				if (T323_1 === 1) {
					result = { b: 'A' };
				} else if (T323_1 === 2) {
					result = { b: 'B' };
				} else if (T323_1 === 3) {
					result = { b: 'C' };
				}
			}
			else {
				result = { err: 'Für eine Bewertung muss ein Wert ausgewählt werden!' };
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_4() {

	Bewertung.call(this);

	this.nr = "3.2.4";
	this.txt = "Ablagerung von Mähgut, Mulchung / Streuschicht";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T324_1 = b.getValue("T324_1");

		if ([1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510].indexOf(lrtCode) >= 0) {
			if (!isNaN(T324_1) && T324_1 > 0) {
				result = 0;
				if (T324_1 === 1) {
					result = { b: 'A' };
				} else if (T324_1 === 2) {
					result = { b: 'B' };
				} else if (T324_1 === 3 || T324_1 === 4) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_5() {

	Bewertung.call(this);

	this.nr = "3.2.5";
	this.txt = "Deckung von Gehölzen (ohne Zwergsträucher und Wacholder)";
	this.lrtCodes = [2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	/*
		2310,2330,4010,4030,5130,6120
			3.2.5 Deckung von Gehölzen (ohne Zwergsträucher und Wacholder)
		6210
			3.2.5 Deckung von Gehölzen (ohne Wacholder) 
		6230	
			3.2.5 Deckung von Gehölzen (ohne Zwergsträucher) 
		6240,6430,6440,6510
			3.2.5 Deckung von Gehölzen 
		6410	
			3.2.5 Deckung von Gehölzen (ohne Kriechweide)
	*/

	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		let titleElement = b.getSectionTitleElement("T325_1");
		if (lrtCode === 6410) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen (ohne Kriechweide)";
		} else if (lrtCode === 6230) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen (ohne Zwergsträucher)";
		} else if (lrtCode === 6210) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen (ohne Wacholder)";
		} else if ([2310, 2330, 4010, 4030, 5130, 6120].indexOf(lrtCode) >= 0) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen (ohne Zwergsträucher und Wacholder)";
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T325_1 = b.getValue("T325_1");
		/*
		2310,2330,4030	<10 <20 <75
		4010,5130,6120,6230,6240,6410,6430,6440,6510 <10 <20 ≤30
		*/

		if (!isNaN(T325_1) && T325_1 > 0) {
			result = 0;
			if ([2310, 2330, 4030].indexOf(lrtCode) >= 0) {
				if (T325_1 === 1 || T325_1 === 2) {
					result = { b: 'A' };
				} else if (T325_1 === 3) {
					result = { b: 'B' };
				} else if ([4, 5, 6].indexOf(T325_1) >= 0) {
					result = { b: 'C' };
				}
			} else if ([4010, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510].indexOf(lrtCode) >= 0) {
				if (T325_1 === 1 || T325_1 === 2) {
					result = { b: 'A' };
				} else if (T325_1 === 3) {
					result = { b: 'B' };
				} else if (T325_1 === 4) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_6() {

	Bewertung.call(this);

	this.nr = "3.2.6";
	this.txt = "Schädigung des (Dünen-)Reliefs";
	this.lrtCodes = [2310, 2330];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T326_1 = b.getValue("T326_1");

		if (!isNaN(T326_1) && T326_1 > 0) {
			result = 0;
			if ([2310, 2330].indexOf(lrtCode) >= 0) {
				if (T326_1 === 1 || T326_1 === 3) {
					result = { b: 'A' };
				} else if (T326_1 === 4) {
					result = { b: 'B' };
				} else if (T326_1 === 5 || T326_1 === 6) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungOffenland_3_2_7() {

	Bewertung.call(this);

	this.nr = "3.2.7";
	this.txt = "Anteil hochwüchsiger Gräser(z. B. Deschampsia flexuosa, Molinia caerulea, Calamagrostis epigejos)";
	this.lrtCodes = [2310, 4010, 4030, 5130];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T327_1 = parseInt(b.getValue("T327_1"));

		if (!isNaN(T327_1) && T327_1 >= 0) {
			result = 0;
			if (lrtCode === 2310) {
				if (T327_1 < 25) {
					result = { b: 'A' };
				} else if (T327_1 >= 25 && T327_1 <= 50) {
					result = { b: 'B' };
				} else if (T327_1 > 50) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 4010) {
				if (T327_1 < 10) {
					result = { b: 'A' };
				} else if (T327_1 >= 10 && T327_1 <= 50) {
					result = { b: 'B' };
				} else if (T327_1 > 50) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 4030) {
				if (T327_1 < 25) {
					result = { b: 'A' };
				} else if (T327_1 >= 25 && T327_1 <= 50) {
					result = { b: 'B' };
				} else if (T327_1 > 50) {
					result = { b: 'C' };
				}

			} else if (lrtCode === 5130) {
				if (T327_1 < 10) {
					result = { b: 'A' };
				} else if (T327_1 >= 10 && T327_1 <= 50) {
					result = { b: 'B' };
				} else if (T327_1 > 50) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungOffenland_3_2_8() {

	Bewertung.call(this);

	this.nr = "3.2.8";
	this.txt = "Deckung von Störzeigern (Ruderalarten, Nitrophyten, Neophyten)";
	this.lrtCodes = [1340, 2310, 2330, 4010, 4030, 5130, 6120, 6210, 6230, 6240, 6410, 6430, 6440, 6510];

	this.berechne = function (b) {
		var T328_1_2 = b.getValue("T328_1_2");
		if (T328_1_2) {
			b.disable("T328_1_1", true);
			document.getElementById(b.layerId + "_t328_1_1_" + b.datensatzNr).value = "";
		} else {
			b.disable("T328_1_1", false);
		}
	}


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T328_1_1 = parseInt(b.getValue("T328_1_1"));
		var T328_1_2 = b.getValue("T328_1_2");


		if (T328_1_2) {
			result = { b: 'A' };
		} else if (!isNaN(T328_1_1) && T328_1_1 >= 0) {
			result = 0;
			if ([1340, 2310, 2330, 4010, 4030, 6120, 6230, 6240, 6410, 6440, 6510].indexOf(lrtCode) >= 0) {
				if (T328_1_1 < 5) {
					result = { "b": "A" };
				} else if (T328_1_1 >= 5 && T328_1_1 <= 10) {
					result = { b: 'B' };
				} else if (T328_1_1 > 10) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 5130) {
				if (T328_1_1 < 5) {
					result = { b: 'A' };
				} else if (T328_1_1 >= 5 && T328_1_1 <= 20) {
					result = { b: 'B' };
				} else if (T328_1_1 > 20) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 6210) {
				if (T328_1_1 < 5) {
					result = { b: 'A' };
				} else if (T328_1_1 >= 5 && T328_1_1 <= 25) {
					result = { b: 'B' };
				} else if (T328_1_1 > 25) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 6430) {
				if (T328_1_1 < 20) {
					result = { b: 'A' };
				} else if (T328_1_1 >= 20 && T328_1_1 <= 50) {
					result = { b: 'B' };
				} else if (T328_1_1 > 50) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

// Moore
function BewertungMoore_1_1_1() {
	Bewertung.call(this);

	this.nr = "1.1.1";
	this.txt = "Deckung lebensraumtypischer Vegetation";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7230];

	this.berechne = function (b) {
		console.log('berechne 1.1.1');
		let lrtCode = b.lrtCode;
		let hc = b.getHauptCode();
		let nc = b.getNebenCodes();
		let aCodes = null;
		let T111_2 = -1;
		let sum = 0;
		for (let i = 0; i < nc.length; i++) {
			// console.info(nc[i]);
		}

		/*
		7120 HC und NC = “MAT, MAG, MDH, MTR, MTO, MSS, MST, MSW, MSP“
		7140 HC und NC = “VRX, MAT, MAG, MDH, MTR, MTO, MSS, MST, MSW, MSP, MZB, MZS, MPB“
		7150 HC und NC = “MSS, TFB“
		7210 HC und NC = “VRC“
		7230 HC und NC = “VRZ, MZK, MZC, MPK“
		*/
		if (lrtCode === 7120) {
			aCodes = ["MAT", "MAG", "MDH", "MTR", "MTO", "MSS", "MST", "MSW", "MSP"];
		} else if (lrtCode === 7140) {
			aCodes = ["VRX", "MAT", "MAG", "MDH", "MTR", "MTO", "MSS", "MST", "MSW", "MSP", "MZB", "MZS", "MPB"];
		} else if (lrtCode === 7150) {
			aCodes = ["MSS", "TFB"];
		} else if (lrtCode === 7210) {
			aCodes = ["VRC"];
		} else if (lrtCode === 7230) {
			aCodes = ["VRZ", "MZK", "MZC", "MPK"];
		}

		if (aCodes.indexOf(hc.code) >= 0) {
			sum += hc.percentage;
		}
		for (let i = 0; i < nc.length; i++) {
			if (aCodes.indexOf(nc[i].code) >= 0) {
				sum += nc[i].percentage;
			}
		}
		console.info("Summe Flächenanteile: " + sum);
		if (Number.isInteger(sum)) {
			if (sum === 100) {
				T111_2 = 1;
			} else if (sum > 90) {
				T111_2 = 2;
			} else if (sum > 75) {
				T111_2 = 3;
			} else if (sum > 49) {
				T111_2 = 4;
			} else if (sum >= 25) {
				T111_2 = 5;
			} else {
				T111_2 = 6;
			}
		}
		b.setTextValue("T111_1", sum);
		b.setRadioButton("T111_2", T111_2);

		// b.disable("T111_1");
		// b.disable("T111_2");
		b.setCalculated("T111_1", "Der Wert wird aus den Anteilen der Flächen mit bestimmten Haupt- bzw. Nebencodes ermittelt.");
		b.setCalculated("T111_2", "Der Wert wird aus den Anteilen der Flächen mit bestimmten Haupt- bzw. Nebencodes ermittelt.");
		// b.setToolTip("T111_1", "Der Wert wird aus den Anteilen der Flächen mit bestimmten Haupt- bzw. Nebencodes ermittelt.");
		// b.setToolTip("T111_2", "Der Wert wird aus den Anteilen der Flächen mit bestimmten Haupt- bzw. Nebencodes ermittelt.");
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T111_2 = b.getValue("T111_2");

		if (!isNaN(T111_2) && T111_2 > 0) {
			result = 0;
			if (lrtCode === 7120) {
				if ([1, 2, 3, 4].indexOf(T111_2) >= 0) {
					result = { b: 'A' };
				} else if (T111_2 === 5) {
					result = { b: 'B' };
				} else if (T111_2 === 6) {
					result = { b: 'C' };
				}
			} else if ([7140, 7230].indexOf(lrtCode) >= 0) {
				if ([1, 2, 3].indexOf(T111_2) >= 0) {
					result = { b: 'A' };
				} else if (T111_2 === 4) {
					result = { b: 'B' };
				} else if ([5, 6].indexOf(T111_2) >= 0) {
					result = { b: 'C' };
				}
			} else if ([7150, 7210].indexOf(lrtCode) >= 0) {
				if ([1, 2].indexOf(T111_2) >= 0) {
					result = { b: 'A' };
				} else if (T111_2 === 3) {
					result = { b: 'B' };
				} else if ([4, 5, 6].indexOf(T111_2) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungMoore_1_1_2() {

	Bewertung.call(this);

	this.nr = "1.1.2";
	this.txt = "Vorkommen der Binsen-Schneide (Cladium mariscus)";
	this.lrtCodes = [7210];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T112_1 = b.getValue("T112_1");

		if (!isNaN(T112_1) && T112_1 > 0) {
			result = 0;
			if (lrtCode === 7210) {
				if ([1, 2, 3].indexOf(T112_1) >= 0) {
					result = { b: 'A' };
				} else if (T112_1 === 4) {
					result = { b: 'B' };
				} else if (T112_1 === 5) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungMoore_1_1_3() {

	Bewertung.call(this);

	this.nr = "1.1.3";
	this.txt = "Deckung von Moosen";
	this.lrtCodes = [7220];

	this.berechne = function (b) {
		let lrtCode = b.lrtCode;
		// console.info("lrtCodfe="+lrtCode);
		if (lrtCode === 7220) {
			let deckung = 0;
			let hc = b.getHauptCode();
			// console.info(hc);
			if ("VQT" === hc.code) {
				deckung += hc.percentage;
			}
			let nc = b.getNebenCodes();
			for (let i = 0; i < nc.length; i++) {
				// console.info(nc[i]);
				if ("VQT" === nc[i].code) {
					deckung += nc[i].percentage;
				}
			}
			// > 75%    > 50%    > 25%    >= 10%    < 10% 
			if (deckung > 75) {
				b.setRadioButton("T113_2", 1);
			} else if (deckung > 50) {
				b.setRadioButton("T113_2", 2);
			} else if (deckung > 25) {
				b.setRadioButton("T113_2", 3);
			} else if (deckung >= 10) {
				b.setRadioButton("T113_2", 4);
			} else {
				b.setRadioButton("T113_2", 5);
			}
			b.setTextValue("T113_1", deckung);

			// b.disable("T113_1", true);
			// b.disable("T113_2", true);
			// b.setToolTip("T113_1", "Der Wert wird aus den Anteilen der Flächen mit dem Haupt- bzw. Nebencode VQT ermittelt.");
			// b.setToolTip("T113_2", "Der Wert wird aus den Anteilen der Flächen mit dem Haupt- bzw. Nebencode VQT ermittelt.");
			b.setCalculated("T113_1", "Der Wert wird aus den Anteilen der Flächen mit dem Haupt- bzw. Nebencode VQT ermittelt.");
			b.setCalculated("T113_2", "Der Wert wird aus den Anteilen der Flächen mit dem Haupt- bzw. Nebencode VQT ermittelt.");

		}
	}

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T113_2 = b.getValue("T113_2");

		if (!isNaN(T113_2) && T113_2 > 0) {
			result = 0;
			if (lrtCode === 7220) {
				if ([1, 2, 3].indexOf(T113_2) >= 0) {
					result = { b: 'A' };
				} else if (T113_2 === 4) {
					result = { b: 'B' };
				} else if (T113_2 === 5) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}


function BewertungMoore_1_2_1() {

	Bewertung.call(this);

	this.nr = "1.2.1";
	this.txt = "Anteil des Biotoptyps Torfmoos-Rasen";
	this.lrtCodes = [7120];

	this.berechne = function (b) {
		let lrtCode = b.lrtCode;
		if (lrtCode === 7120) {
			let deckung = 0;
			let hc = b.getHauptCode();
			if ("MAT" === hc.code) {
				deckung += hc.percentage;
			}
			let nc = b.getNebenCodes();
			for (let i = 0; i < nc.length; i++) {
				if ("MAT" === nc[i].code) {
					deckung += nc[i].percentage;
				}
			}
			// > 50%    > 25%    >= 10%    < 10%    fehlend    ;
			if (deckung > 50) {
				b.setRadioButton("T121_1", 1);
			} else if (deckung > 25) {
				b.setRadioButton("T121_1", 2);
			} else if (deckung >= 10) {
				b.setRadioButton("T121_1", 3);
			} else if (deckung < 10 && deckung > 0) {
				b.setRadioButton("T121_1", 4);
			} else {
				b.setRadioButton("T121_1", 5);
			}

			// b.setToolTip("T121_1", "Der Wert wird aus dem Anteil der Fläche mit dem Haupt- bzw. Nebencode MAT ermittelt.");
			b.setCalculated("T121_1", "Der Wert wird aus dem Anteil der Fläche mit dem Haupt- bzw. Nebencode MAT ermittelt.");
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T121_1 = b.getValue("T121_1");

		if (!isNaN(T121_1) && T121_1 > 0) {
			result = 0;
			if (lrtCode === 7120) {
				if ([1, 2, 3].indexOf(T121_1) >= 0) {
					result = { b: 'A' };
				} else if (T121_1 === 4) {
					result = { b: 'B' };
				} else if (T121_1 === 5) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungMoore_1_2_2() {

	Bewertung.call(this);

	this.nr = "1.2.2";
	this.txt = "Vorkommen von Nassstellen / Schlenken";
	this.lrtCodes = [7140];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T122 = b.getValue("T122");

		if (!isNaN(T122) && T122 > 0) {
			result = 0;
			if (lrtCode === 7140) {
				if ([1, 2, 3].indexOf(T122) >= 0) {
					result = { b: 'A' };
				} else if (T122 === 4) {
					result = { b: 'B' };
				} else if (T122 === 5) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungMoore_1_3_1() {

	Bewertung.call(this);

	this.nr = "1.3.1";
	this.txt = "Quellaktivität";
	this.lrtCodes = [7220, 7230];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T131_1 = b.getValue("T131_1");

		if (!isNaN(T131_1) && T131_1 > 0) {
			result = 0;
			if ([7220, 7230].indexOf(lrtCode) >= 0) {
				if (T131_1 === 1) {
					result = { b: 'A' };
				} else if (T131_1 === 2) {
					result = { b: 'B' };
				} else if (T131_1 === 3) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}


function BewertungMoore_1_3_2() {

	Bewertung.call(this);

	this.nr = "1.3.2";
	this.txt = "Überrieselung";
	this.lrtCodes = [7220];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T132_1 = b.getValue("T132_1");

		if (!isNaN(T132_1) && T132_1 > 0) {
			result = 0;
			if (lrtCode === 7220) {
				if ([1, 2, 3].indexOf(T132_1) >= 0) {
					result = { b: 'A' };
				} else if (T132_1 === 4) {
					result = { b: 'B' };
				} else if (T132_1 === 5) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungMoore_2_1_1() {

	Bewertung.call(this);

	this.nr = "2.1.1";
	this.txt = "Gesamtanzahl lr-typischer und Anzahl besonders charakteristischer Pflanzenarten";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7230, 7220, 7230];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;

		var T211_1_1 = parseInt(b.getValue("T211_1_1"));
		var T211_1_2 = parseInt(b.getValue("T211_1_2"));
		var T211_3_1 = parseInt(b.getValue("T211_3_1"));
		var T211_3_2 = parseInt(b.getValue("T211_3_2"));
		var T211_4 = parseInt(b.getValue("T211_4"));

		var hc = b.getValue("hc");

		if (lrtCode === 7120) {
			if (!isNaN(T211_1_2) && !isNaN(T211_3_2)) {
				result = 0;
				if (T211_1_2 >= 10 && T211_3_2 >= 5) {
					result = { b: 'A' };
				} else if (T211_1_2 >= 5 & T211_3_2 >= 3) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 7140) {
			if (!isNaN(T211_1_2) && !isNaN(T211_1_1) && !isNaN(T211_3_2)) {
				result = 0;
				T211_1_3 = b.getValue("T211_1_3");
				if (T211_1_3) { // Schnabelsegge
					if (T211_1_1 > 2 && T211_1_2 >= 1) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 2 && T211_1_2 >= 1) {
						result = { b: 'B' };
					} else {
						result = { b: 'C' };
					}
				} else {
					if (T211_1_2 >= 7 && T211_3_2 >= 2) {
						result = { b: 'A' };
					} else if (T211_1_2 >= 4 && T211_3_2 >= 1) {
						result = { b: 'B' };
					} else {
						result = { b: 'C' };
					}
				}
			}
		} else if (lrtCode === 7150) {
			if (!isNaN(T211_1_2) && !isNaN(T211_3_2) && !isNaN(T211_1_1)) {
				result = 0;
				if (hc === 'TFB') {
					if (T211_1_1 >= 4 && T211_1_2 >= 2) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 2 && T211_1_2 >= 1) {
						result = { b: 'B' };
					} else if (T211_1_2 === 1) {
						result = { b: 'C' };
					}
				} else {
					if (T211_1_2 >= 3 && T211_3_2 >= 2) {
						result = { b: 'A' };
					} else if (T211_1_2 === 2 && T211_3_2 >= 2) {
						result = { b: 'B' };
					} else {
						result = { b: 'C' };
					}
				}
			}
		} else if (lrtCode === 7210) {
			if (hc === 'VRC') {
				if (!isNaN(T211_1_1) && !isNaN(T211_4)) {
					result = 0;
					if (T211_1_1 >= 8 && T211_4 >= 2) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 6 && T211_4 >= 1) {
						result = { b: 'B' };
					} else {
						result = { b: 'C' };
					}
				}
			} else {
				if (!isNaN(T211_1_1) && !isNaN(T211_3_1) && !isNaN(T211_4)) {
					result = 0;
					if (T211_1_1 >= 8 && T211_4 >= 2 && T211_3_1 >= 3) {
						result = { b: 'A' };
					} else if (T211_1_1 >= 6 && T211_4 >= 1 && T211_3_1 >= 1) {
						result = { b: 'B' };
					} else {
						result = { b: 'C' };
					}
				}
			}
		} else if (lrtCode === 7220) {
			if (!isNaN(T211_3_1)) {
				result = 0;
				if (T211_3_1 >= 3) {
					result = { b: 'A' };
				} else if (T211_3_1 === 2) {
					result = { b: 'B' };
				} else if (T211_3_1 === 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 7230) {
			if (!isNaN(T211_1_2) && !isNaN(T211_3_2)) {
				result = 0;
				if (T211_1_2 >= 7 && T211_3_2 >= 2) {
					result = { b: 'A' };
				} else if (T211_1_2 >= 4 && T211_3_2 >= 1) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungMoore_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Tierarten";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7220, 7230];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {
		var T22_1 = b.getValue("T22_1");
		var T22_2 = b.getTextValue("T22_2");
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
	}
}

function BewertungMoore_3_1_1() {

	Bewertung.call(this);

	this.nr = "3.1.1";
	this.txt = "Veränderung des Wasserregimes";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7230];

	this.berechne = function (b) {
		if (b.hasGefcode('YWO')) {
			b.setRadioButton("T311_1_1", "t");
		}
		else {
			b.setRadioButton("T311_1_1", "f");
		}
		if (b.hasGefcode('YWE') || b.hasGefcode('YWF')) {
			b.setRadioButton("T311_1_2", "t");
		}
		else {
			b.setRadioButton("T311_1_2", "f");
		}
		if (b.hasGefcode('YWZ') || b.hasGefcode('YWI')) {
			b.setRadioButton("T311_1_3", "t");
		}
		else {
			b.setRadioButton("T311_1_3", "f");
		}
		if (b.hasGefcode('YWS')) {
			b.setRadioButton("T311_1_4", "t");
		}
		else {
			b.setRadioButton("T311_1_4", "f");
		}
		if (b.hasGefcode('YWG')) {
			b.setRadioButton("T311_1_5", "t");
		}
		else {
			b.setRadioButton("T311_1_5", "f");
		}
		b.setCalculated("T311_1_1", "Aus Grundbogen - ja, wenn Gefährdungscode YWD");
		b.setCalculated("T311_1_2", "Aus Grundbogen - ja, wenn Gefährdungscode YWE oder YWF");
		b.setCalculated("T311_1_3", "Aus Grundbogen - ja, wenn Gefährdungscode YWZ oder YWI");
		b.setCalculated("T311_1_4", "Aus Grundbogen - ja, wenn Gefährdungscode YWS");
		b.setCalculated("T311_1_5", "Aus Grundbogen - ja, wenn Gefährdungscode YWG");
	}


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T311_2 = b.getValue("T311_2");

		if (lrtCode === 7120) {
			if (!isNaN(T311_2) && T311_2 > 0) {
				result = 0;
				if (T311_2 === 1) {
					result = { b: 'A' };
				} else if (T311_2 === 2) {
					result = { b: 'B' };
				} else if (T311_2 === 3) {
					result = { b: 'C' };
				}
			}
		} else if ([7140, 7150, 7210, 7230].indexOf(lrtCode) >= 0) {
			var T311_1_1 = b.getBoolValue("T311_1_1");
			var T311_1_2 = b.getBoolValue("T311_1_2");
			var T311_1_3 = b.getBoolValue("T311_1_3");
			var T311_1_4 = b.getBoolValue("T311_1_4");
			var T311_1_5 = b.getBoolValue("T311_1_5");

			if (T311_1_1 != -1 && T311_1_2 != -1 && T311_1_3 != -1 && T311_1_4 != -1 && T311_1_5 != -1) {
				if (T311_1_1 || T311_1_2 || T311_1_5) {
					result = { b: 'C' };
					b.setRadioButton("T311_1_6", false);
				} else if (T311_1_3 || T311_1_4) {
					result = { b: 'B' };
					b.setRadioButton("T311_1_6", false);
				} else {
					var T311_1_6 = b.getValue("T311_1_6");
					if (T311_1_6 === true) {
						result = { b: 'A' };
					} else {
						result = { err: 'Bestätigen Sie, dass keine Beeinträchtigungen erkennbar sind oder wählen Sie die Gefährdungen im Grundbogen!' };
					}
				}
			}
		}
		this.result = result;
	}
}

function BewertungMoore_3_2_1() {

	Bewertung.call(this);

	this.nr = "3.2.1";
	this.txt = "Stoffeinträge möglich durch fehlende Pufferstrukturen (außerhalb des LRT)";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7220, 7230];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {
		var T321_2 = b.getValue("T321_2");
		this.korrekturWert = [4, 5, 6].indexOf(T321_2) >= 0 ? -1 : 0;
	}

}

function BewertungMoore_3_2_2() {

	Bewertung.call(this);

	this.nr = "3.2.2";
	this.txt = "Schädigung von Vegetation und Strukturen";
	this.lrtCodes = [7140, 7150, 7210, 7220, 7230];

	this.berechne = function (b) {
		var T322_1 = b.getValue("T322_1");
		var T322_2 = b.getValue("T322_2");
		var T322_3 = b.getValue("T322_3");
		var T322_4 = b.getValue("T322_4");

		if (T322_1 > T322_4 || T322_2 > T322_4 || T322_3 > T322_4) {
			b.setRadioButton("T322_4", null);
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T322_1 = b.getValue("T322_1");
		var T322_2 = b.getValue("T322_2");
		var T322_3 = b.getValue("T322_3");
		var T322_4 = b.getValue("T322_4");


		if (!isNaN(T322_4) && T322_4 > 0) {
			result = 0;
			if (T322_4 === 1) {
				result = { b: 'A' };
			} else if (T322_4 === 3) {
				result = { b: 'B' };
			} else if ([4, 5, 6].indexOf(T322_4) >= 0) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungMoore_3_2_3() {

	Bewertung.call(this);

	this.nr = "3.2.3";
	this.txt = "Höhenunterschiede durch Torfabbau";
	this.lrtCodes = [7120];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T323_1 = b.getValue("T323_1");

		if (!isNaN(T323_1) && T323_1 > 0) {
			result = 0;
			if (T323_1 === 1 || T323_1 === 2) {
				result = { b: 'A' };
			} else if (T323_1 === 3) {
				result = { b: 'B' };
			} else if (T323_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungMoore_3_2_4() {

	Bewertung.call(this);

	this.nr = "3.2.4";
	this.txt = "Landwirtschaftliche Nutzung / Pflege";
	this.lrtCodes = [7230];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T324_1 = b.getValue("T324_1");

		if (!isNaN(T324_1) && T324_1 > 0) {
			result = 0;
			if (T324_1 === 1 || T324_1 === 2) {
				result = { b: 'A' };
			} else if (T324_1 === 3) {
				result = { b: 'B' };
			} else if (T324_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungMoore_3_2_5() {

	Bewertung.call(this);

	this.nr = "3.2.5";
	this.txt = "Deckung von Gehölzen (außer Zwergsträucher und Sumpf-Porst)";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7230];

	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		let titleElement = b.getSectionTitleElement("T325_1");
		if (lrtCode === 7120 || lrtCode === 7140) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen ohne Zwergsträucher, Sumpf-Porst, Trunkelbeere";
		} else if (lrtCode === 7150) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen ohne Zwergsträucher";
		} else if (lrtCode === 7230) {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen ohne Strauch-Birke, Kriech-Weide";
		} else {
			titleElement.innerText = "3.2.5 Deckung von Gehölzen";
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T325_1 = b.getValue("T325_1");

		if (!isNaN(T325_1) && T325_1 > 0) {
			result = 0;
			if ([7120, 7140].indexOf(lrtCode) >= 0) {
				if ([1, 2, 3, 4].indexOf(T325_1) >= 0) {
					result = { b: 'A' };
				} else if ([5, 6].indexOf(T325_1) >= 0) {
					result = { b: 'B' };
				} else if ([7, 8].indexOf(T325_1) >= 0) {
					result = { b: 'C' };
				}
			} else if ([7150, 7210].indexOf(lrtCode) >= 0) {
				if ([1, 2].indexOf(T325_1) >= 0) {
					result = { b: 'A' };
				} else if (T325_1 === 3) {
					result = { b: 'B' };
				} else if ([4, 5, 6, 7, 8].indexOf(T325_1) >= 0) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 7230) {
				if ([1, 2].indexOf(T325_1) >= 0) {
					result = { b: 'A' };
				} else if ([3, 4, 5, 6].indexOf(T325_1) >= 0) {
					result = { b: 'B' };
				} else if ([7, 8].indexOf(T325_1) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungMoore_3_2_8() {

	Bewertung.call(this);

	this.nr = "3.2.8";
	this.txt = "3.2.8 Deckung von Störzeigern (Nitrophyten, Neophyten)";
	this.lrtCodes = [7120, 7140, 7150, 7210, 7220, 7230];

	this.berechne = function (b) {
		var T328_1_2 = b.getValue("T328_1_2");
		if (T328_1_2) {
			b.disable("T328_1_1", true);
			document.getElementById(b.layerId + "_t328_1_1_" + b.datensatzNr).value = "";
		} else {
			b.disable("T328_1_1", false);
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T328_1 = parseInt(b.getValue("T328_1_1"));
		var T328_1_2 = b.getValue("T328_1_2");

		if (T328_1_2) {
			result = { b: 'A' };
		} else if (!isNaN(T328_1) && T328_1 > 0) {
			result = 0;
			if (T328_1 < 5) {
				result = { b: 'A' };
			} else if (T328_1 >= 5 && T328_1 <= 10) {
				result = { b: 'B' };
			} else if (T328_1 > 10) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

// Küste
function BewertungKueste_1_1_1() {

	Bewertung.call(this);

	this.nr = "1.1.1";
	this.txt = "Deckung lebensraumtypischer Vegetation";
	this.lrtCodes = [1330, 2130, 2140, 2150, 2160, 2170, 2190];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T111_1 = b.getValue("T111_1");

		if (!isNaN(T111_1) && T111_1 > 0) {
			result = 0;
			if (lrtCode === 1330) {
				if ([1, 2, 3].indexOf(T111_1) >= 0) {
					result = { b: 'A' };
				} else if (T111_1 === 4) {
					result = { b: 'B' };
				} else if (T111_1 === 5) {
					result = { b: 'C' };
				}
			} else if ([2130, 2140, 2150, 2160, 2170, 2190].indexOf(lrtCode) >= 0) {
				if ([1, 2].indexOf(T111_1) >= 0) {
					result = { b: 'A' };
				} else if (T111_1 === 3) {
					result = { b: 'B' };
				} else if ([4, 5].indexOf(T111_1) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_1_2() {

	Bewertung.call(this);

	this.nr = "1.1.2";
	this.txt = "Deckung lebensraumtypischer Zwergsträucher / Sträucher";
	this.lrtCodes = [2140, 2150, 2160, 2170];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T112_1 = b.getValue("T112_1");

		if (!isNaN(T112_1) && T112_1 > 0) {
			result = 0;
			if ([1, 2].indexOf(T112_1) >= 0) {
				result = { b: 'A' };
			} else if (T112_1 === 3) {
				result = { b: 'B' };
			} else if (T112_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_1_3() {

	Bewertung.call(this);

	this.nr = "1.1.3";
	this.txt = "Deckung Queller und Strand-Sode";
	this.lrtCodes = [1310];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T113_1 = b.getValue("T113_1");

		if (!isNaN(T113_1) && T113_1 > 0) {
			result = 0;
			if ([1, 2, 3].indexOf(T113_1) >= 0) {
				result = { b: 'A' };
			} else if (T113_1 === 4) {
				result = { b: 'B' };
			} else if (T113_1 === 5) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}


function BewertungKueste_1_2_1() {

	Bewertung.call(this);

	this.nr = "1.2.1";
	this.txt = "Altersstruktur des Heidekrauts (Calluna vulgaris) und Sanddorns (Hippophaë rhamnoides)";
	this.lrtCodes = [2150, 2160];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T121_1 = b.getValue("T121_1");
		var T121_2 = b.getValue("T121_2");

		if (!isNaN(T121_1) && T121_1 > 0) {
			if (T121_1 === 1) {
				if (!isNaN(T121_2) && T121_2 > 0) {
					result = 0;
					if (T121_2 === 3) {
						result = { b: 'A' };
					} else if (T121_2 === 2) {
						result = { b: 'B' };
					} else if (T121_2 === 1) {
						result = { b: 'C' };
					}
				} else {
					b.disable("T121_2", false);
				}
			}
			else {
				result = { b: 'C' };
				b.disable("T121_2", true);
			}
		} else {
			b.disable("T121_2", true);
		}
		this.result = result;
	}

	this.prepareHTML = function (b) {
		let lrtCode = b.lrtCode;
		let titleElement = b.getSectionTitleElement("T121_1");
		if (lrtCode === 2150) {
			titleElement.innerText = "1.2.1 Altersstruktur des Heidekrauts (Calluna vulgaris)";
		} else if (lrtCode === 2160) {
			titleElement.innerText = "1.2.1 Altersstruktur des Sanddorns (Hippophaё rhamnoides)";
		}
	}
}

function BewertungKueste_1_3_1() {

	Bewertung.call(this);

	this.nr = "1.3.1";
	this.txt = "1.3.1 Auftretende Substrate / Substratdiversität";
	this.lrtCodes = [1220];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T131_1 = b.getValue("T131_1");

		if (Array.isArray(T131_1)) {
			result = 0;
			if (T131_1.length >= 4) {
				result = { b: 'A' };
			} else if (T131_1.length >= 2) {
				result = { b: 'B' };
			} else if (T131_1.length === 1) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_4_1() {

	Bewertung.call(this);

	this.nr = "1.4.1";
	this.txt = "Natürliche Morphodynamik (Rutschungen, Spülprozesse, Abbrüche, Solifluktion, Kliffranddünenbildung)";
	this.lrtCodes = [1230];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T141_1 = b.getValue("T141_1");

		if (!isNaN(T141_1) && T141_1 > 0) {
			result = 0;
			if ([1, 2, 3].indexOf(T141_1) >= 0) {
				result = { b: 'A' };
			} else if (T141_1 === 4) {
				result = { b: 'B' };
			} else if (T141_1 === 5) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_4_2() {

	Bewertung.call(this);

	this.nr = "1.4.2";
	this.txt = "Neulandbildung im Watt";
	this.lrtCodes = [1310];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T142_1 = b.getValue("T142_1");

		if (!isNaN(T142_1) && T142_1 > 0) {
			if (T142_1 === 1) {
				var T142_2 = b.getValue("T142_2");
				if (T142_2 === 1) {
					result = { b: 'A' };
				} else if (T142_2 === 2) {
					result = { b: 'B' };
				}
			}
			else {
				result = { b: 'C' };
				b.setRadioButton("T142_2", null);
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_4_3() {

	Bewertung.call(this);

	this.nr = "1.4.3";
	this.txt = "Überflutungsdynamik";
	this.lrtCodes = [1310, 1330];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T143 = b.getValue("T143");

		if (!isNaN(T143) && T143 > 0) {
			result = 0;
			if (T143 === 1) {
				result = { b: 'A' };
			} else if (T143 === 2) {
				result = { b: 'B' };
			} else if (T143 === 3) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_4_4() {

	Bewertung.call(this);

	this.nr = "1.4.4";
	this.txt = "Sandeinblasung";
	this.lrtCodes = [2110, 2120];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T144 = b.getValue("T144");

		if (!isNaN(T144) && T144 > 0) {
			result = 0;
			if (T144 === 1) {
				result = { b: 'A' };
			} else if (T144 === 2 || T144 === 3) {
				result = { b: 'B' };
			} else if (T144 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_4_5() {

	Bewertung.call(this);

	this.nr = "1.4.5";
	this.txt = "Vorhandene Dünenabfolge ggf. mit kleinflächig auftretenden nachfolgenden Dünenstadien";
	this.lrtCodes = [2130, 2140, 2150, 2160, 2170, 2190];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T145 = b.getValue("T145");
		var anz = 0;

		if (Array.isArray(T145)) {
			result = 0;
			if ([2130, 2140, 2150, 2160, 2170].indexOf(lrtCode) >= 0) {
				if (T145.indexOf(1) >= 0) { anz++; }
				if (T145.indexOf(2) >= 0) { anz++; }
				if (T145.indexOf(3) >= 0) { anz++; }
				if (anz >= 3) {
					result = { b: 'A' };
				} else if (anz === 2) {
					result = { b: 'B' };
				} else if (anz === 1) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 2190) {
				if (T145.indexOf(1) >= 0 && T145.indexOf(2) >= 0) {
					result = { b: 'A' };
				} else if (T145.indexOf(2) >= 0) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_5_1() {

	Bewertung.call(this);

	this.nr = "1.5.1";
	this.txt = "Struktur des Spülsaumes";
	this.lrtCodes = [1210];



	this.berechne = function (b) {
		if (b.hasHabitat("CKW")) {
			b.setRadioButton("T151_1", "t");

			if (b.hasHabitat("CKR")) {
				b.setRadioButton("T151_2", "2");
			}
			else if (b.hasHabitat("CKF")) {
				b.setRadioButton("T151_2", "3");
			}
			else {
				b.setRadioButton("T151_2", "1");
			}
		}
		else {
			b.setRadioButton("T151_1", "f");
			b.setRadioButton("T151_2", null);
		}
		b.setCalculated("T151_1", "Ergibt sich aus den Habitaten");
		b.setCalculated("T151_2", "Ergibt sich aus den Habitaten");
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T151_1 = b.getBoolValue("T151_1");
		var T151_2 = b.getValue("T151_2");



		if (typeof (T151_1) === 'boolean') {
			if (T151_1) {
				if (!isNaN(T151_2) && T151_2 > 0) {
					result = 0;
					if (T151_2 === 2 || T151_2 === 3) {
						result = { b: 'A' };
					} else if (T151_2 === 1) {
						result = { b: 'B' };
					}
				} else {
					// b.disable('T151_2', false);
				}

			}
			else {
				result = { b: 'C' };
				// b.disable('T151_2', true);
			}
		} else {
			// b.disable('T151_2', true);
		}
		this.result = result;
	}
}


function BewertungKueste_1_5_2() {

	Bewertung.call(this);

	this.nr = "1.5.2";
	this.txt = "Strukturvielfalt (Anzahl der Biotoptypen verschiedener Hauptgruppen und vegetationsfreier Rohboden)";
	this.lrtCodes = [1230];

	this.prepareHTML = function (b) {
		b.setCalculated("T152", "Ergibt sich aus den Haupt- und Nebencodes");
	};

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T152 = b.getValue("T152");

		var anz = b.getAnzahlNebenCodes();
		//var anz = parseInt(b.getValue("anzahl_nc"));
		var hc = b.getValue("hc");
		console.info('anz: ' + anz + ' hc: ' + hc + ' t152: ' + T152);
		if (hc && hc.length >= 3) {
			anz += 1;
			if (anz >= 7) {
				b.setRadioButton("T152", 1);
				result = { b: 'A' };
			} else if (anz >= 5) {
				b.setRadioButton("T152", 2);
				result = { b: 'B' };
			} else if (anz >= 3) {
				b.setRadioButton("T152", 3);
				result = { b: 'B' };
			} else {
				b.setRadioButton("T152", 4);
				result = { b: 'C' };
			}
		}
		// b.setRadioButton("T152", 4);
		this.result = result;
	}
}



function BewertungKueste_1_5_3() {

	Bewertung.call(this);

	this.nr = "1.5.3";
	this.txt = "Salzwiesenrelief";
	this.lrtCodes = [1330];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T153 = b.getValue("T153");
		var hc = b.getValue("hc");

		if (!isNaN(T153) && T153 > 0) {
			result = 0;
			if (T153 === 1) {
				result = { b: 'A' };
			} else if (T153 === 2) {
				result = { b: 'B' };
			} else if (T153 === 3) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_5_4() {

	Bewertung.call(this);

	this.nr = "1.5.4";
	this.txt = "Zonierung Ufer und Salzgrünland bzw. Zonierung Dünental";
	this.lrtCodes = [1330, 2190];


	this.berechne = function (b) {
		var lrtCode = b.lrtCode;
		if (lrtCode === 2190) {
			if (b.hasHabitat("CDZ")) {
				b.setRadioButton("T154_2", 1);
			}
			else {
				b.setRadioButton("T154_2", 2);
			}
			// let elements = b.getElements("T154_2");
			// elements[0].parentElement.style.backgroundColor=colorDisabled;
			// elements[0].parentElement.title = "Aus Codierungen für Habitate und Strukturen abgeleitet.";
			// b.disable("T154_2");
			b.setCalculated("T154_2", "Aus Codierungen für Habitate und Strukturen abgeleitet.");
		}
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T154_1, T154_2, T211_1_3;

		if (lrtCode === 1330) {
			T154_1 = b.getValue("T154_1");
			T211_1_3 = parseInt(b.getValue("T211_1_3"));

			if (Array.isArray(T154_1) && !isNaN(T211_1_3) && T211_1_3 > 0) {
				result = 0;
				if (T154_1.length >= 4 && T211_1_3 === 1 || T154_1.length >= 3 && T211_1_3 === 2) {
					result = { b: 'A' };
				} else if (T154_1.length >= 2 && T211_1_3 === 1 || T154_1.length === 2 && T211_1_3 === 2) {
					result = { b: 'B' };
				} else if (T154_1.length === 1) {
					result = { b: 'C' };
				}
			} else if (isNaN(T211_1_3) || T211_1_3 === -1) {
				result = { b: 0, err: "unter 2.1.1 muss die Region gewählt sein" };
			}

		} else if (lrtCode === 2190) {
			T154_2 = b.getValue("T154_2");
			if (!isNaN(T154_2) && T154_2 > 0) {
				result = 0;
				if (T154_2 === 1) {
					result = { b: 'A' };
				} else if (T154_2 === 2) {
					result = { b: 'C' };
				}
			}
		}
		console.info(result);
		this.result = result;
	}
}

function BewertungKueste_1_5_5() {

	Bewertung.call(this);

	this.nr = "1.5.5";
	this.txt = "Dünenrelief";
	this.lrtCodes = [2110, 2120];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T155_1 = b.getValue("T155_1");

		if (!isNaN(T155_1) && T155_1 > 0) {
			result = 0;
			if (lrtCode === 2110) {
				if ([3, 4, 5].indexOf(T155_1) >= 0) {
					result = { b: 'A' };
				} else if (T155_1 === 6) {
					result = { b: 'B' };
				} else if (T155_1 === 7) {
					result = { b: 'C' };
				}
			} else if (lrtCode === 2120) {
				if (T155_1 === 3) {
					result = { b: 'A' };
				} else if (T155_1 === 4) {
					result = { b: 'B' };
				} else if ([5, 6, 7].indexOf(T155_1) >= 0) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungKueste_1_5_6() {

	Bewertung.call(this);

	this.nr = "1.5.6";
	this.txt = "Flächenanteil mit typischem Dünenrelief";
	this.lrtCodes = [2110, 2120, 2130, 2140, 2150, 2160, 2170];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T156 = b.getValue("T156");

		if (!isNaN(T156) && T156 > 0) {
			result = 0;
			if (T156 === 1) {
				result = { b: 'A' };
			} else if (T156 === 2) {
				result = { b: 'B' };
			} else if (T156 === 3) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_2_1_1() {

	Bewertung.call(this);

	this.nr = "2.1.1";
	this.txt = "Gesamtanzahl lr-typischer und Anzahl besonders charakteristischer Pflanzenarten";
	this.lrtCodes = [1210, 1220, 1230, 1310, 1330, 2110, 2120, 2130, 2140, 2150, 2160, 2170, 2190];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T211_1_1 = parseInt(b.getValue("T211_1_1"));
		var T211_1_2 = parseInt(b.getValue("T211_1_2"));
		var T211_3_1;

		if (lrtCode === 1210) {
			if (!isNaN(T211_1_1) && !isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 6) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 6 && T211_1_2 >= 3) {
					result = { b: 'B' };
				} else if (T211_1_1 >= 1 && T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 1220) {
			if (!isNaN(T211_1_1) && !isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_1 >= 15 && T211_1_2 >= 6) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 10 && T211_1_2 >= 3) {
					result = { b: 'B' };
				} else if (T211_1_2 >= 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 1230) {
			if (!isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_2 >= 10) {
					result = { b: 'A' };
				} else if (T211_1_2 >= 5) {
					result = { b: 'B' };
				} else if (T211_1_2 < 5) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 1310) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 5) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_1 = 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 1330) {
			T211_1_3 = parseInt(b.getValue("T211_1_3"));
			console.log('T211_1_3: ', T211_1_3);
			if (isNaN(T211_1_3) || T211_1_3 === -1) {
				result = { b: 0, err: "Es muss eine Region Ost/West gewählt sein." };
			}
			else {
				if (!isNaN(T211_1_2)) {
					result = 0;
					if (T211_1_3 === 1) {
						// console.info("West");
						if (T211_1_2 >= 10) {
							result = { b: 'A' };
						} else if (T211_1_2 >= 6) {
							result = { b: 'B' };
						} else if (T211_1_2 < 6) {
							result = { b: 'C' };
						}
					} else if (T211_1_3 === 2) {
						// console.info("Ost");
						if (T211_1_2 >= 7) {
							result = { b: 'A' };
						} else if (T211_1_2 >= 3) {
							result = { b: 'B' };
						} else if (T211_1_2 < 3) {
							result = { b: 'C' };
						}
					}
				}
			}
		} else if (lrtCode === 2110) {
			if (!isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_2 >= 4) {
					result = { b: 'A' };
				} else if (T211_1_2 >= 2) {
					result = { b: 'B' };
				} else if (T211_1_2 === 1) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2120) {
			if (!isNaN(T211_1_1) && !isNaN(T211_1_2)) {
				result = 0;
				if (T211_1_1 >= 10 && T211_1_2 >= 5) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 3) {
					result = { b: 'B' };
				} else /* if (T211_1_2 < 3  || T211_1_1<5) */ {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2130) {
			if (!isNaN(T211_1_1) && !isNaN(T211_1_2)) {
				result = 0;
				// ≥ 5 / 10 Arten ≥ 3 / 5 Arten < 3 / 5 Arten 
				if (T211_1_1 >= 10 && T211_1_2 >= 5) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5 && T211_1_2 >= 3) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2140) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 13) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 7) {
					result = { b: 'B' };
				} else if (T211_1_1 < 7) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2150) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 8) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5) {
					result = { b: 'B' };
				} else if (T211_1_1 < 5) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2160) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 9) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 5) {
					result = { b: 'B' };
				} else if (T211_1_1 < 5) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2170) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 6) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 4) {
					result = { b: 'B' };
				} else if (T211_1_1 < 4) {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === 2190) {
			if (!isNaN(T211_1_1)) {
				result = 0;
				if (T211_1_1 >= 15) {
					result = { b: 'A' };
				} else if (T211_1_1 >= 10) {
					result = { b: 'B' };
				} else if (T211_1_1 < 10) {
					result = { b: 'C' };
				}
			}
		}

		//	} 
		this.result = result;
	}
}

function BewertungKueste_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Tierarten";
	this.lrtCodes = [1210, 1220, 1230, 1310, 1330, 2110, 2120, 2130, 2140, 2150, 2160, 2170, 2190];

	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {
		var T22_1 = b.getValue("T22_1");
		var T22_2 = b.getTextValue("T22_2");
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
	}
}

function BewertungKueste_3_1_1() {

	Bewertung.call(this);

	this.nr = "3.1.1";
	this.txt = "Veränderung des Wasserregimes";
	this.lrtCodes = [1310, 1330];

	this.berechne = function (b) {
		if (b.hasGefcode('YWO')) {
			b.setRadioButton("T311_1_1", "t");
		}
		else {
			b.setRadioButton("T311_1_1", "f");
		}
		if (b.hasGefcode('YWE') || b.hasGefcode('YWF')) {
			b.setRadioButton("T311_1_2", "t");
		}
		else {
			b.setRadioButton("T311_1_2", "f");
		}
		if (b.hasGefcode('YWZ') || b.hasGefcode('YWI')) {
			b.setRadioButton("T311_1_3", "t");
		}
		else {
			b.setRadioButton("T311_1_3", "f");
		}
		if (b.hasGefcode('YWS')) {
			b.setRadioButton("T311_1_4", "t");
		}
		else {
			b.setRadioButton("T311_1_4", "f");
		}
		if (b.hasGefcode('YWG')) {
			b.setRadioButton("T311_1_5", "t");
		}
		else {
			b.setRadioButton("T311_1_5", "f");
		}
		if (b.hasGefcode('YWD')) {
			b.setRadioButton("T311_1_6", "t");
		}
		else {
			b.setRadioButton("T311_1_6", "f");
		}
		b.setCalculated("T311_1_1", "Aus Grundbogen - ja, wenn Gefährdungscode YWO");
		b.setCalculated("T311_1_2", "Aus Grundbogen - ja, wenn Gefährdungscode YWF");
		b.setCalculated("T311_1_3", "Aus Grundbogen - ja, wenn Gefährdungscode YWZ oder YWI");
		b.setCalculated("T311_1_4", "Aus Grundbogen - ja, wenn Gefährdungscode YWS");
		b.setCalculated("T311_1_5", "Aus Grundbogen - ja, wenn Gefährdungscode YWG");
		b.setCalculated("T311_1_6", "Aus Grundbogen - ja, wenn Gefährdungscode YWD");
	}

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T311_1_1 = b.getBoolValue("T311_1_1");
		var T311_1_2 = b.getBoolValue("T311_1_2");
		var T311_1_3 = b.getBoolValue("T311_1_3");
		var T311_1_4 = b.getBoolValue("T311_1_4");
		var T311_1_5 = b.getBoolValue("T311_1_5");
		var T311_1_6 = b.getBoolValue("T311_1_6");
		if (T311_1_1 !== -1 && T311_1_2 !== -1 && T311_1_3 !== -1 && T311_1_4 !== -1 && T311_1_5 !== -1 && T311_1_6 !== -1) {
			this.result = 0;
			if (!T311_1_1 && !T311_1_2 && !T311_1_3 && !T311_1_4 && !T311_1_5 && !T311_1_6) {
				var T311_1_7 = b.getValue("T311_1_7");
				if (T311_1_7 === true) {
					result = { b: 'A' };
				} else {
					result = { err: 'Bestätigen Sie, dass keine Beeinträchtigungen erkennbar sind oder wählen Sie die Gefährdungen im Grundbogen!' };
				}
			} else if (T311_1_2 || T311_1_1 || T311_1_5) {
				result = { b: 'C' };
				b.setRadioButton("T311_1_7", false);
			} else {
				result = { b: 'B' };
				b.setRadioButton("T311_1_7", false);
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_1() {

	Bewertung.call(this);

	this.nr = "3.2.1";
	this.txt = "Schädigung von Vegetation und Strukturen";
	this.lrtCodes = [1210, 1220, 1230, 1310, 1330, 2110, 2120, 2130, 2140, 2150, 2160, 2170, 2190];

	this.berechne = function (b) {
		var T321_1 = b.getValue("T321_1");
		var T321_2 = b.getValue("T321_2");
		var T321_3 = b.getValue("T321_3");
		var T321_4 = b.getValue("T321_4");
		/*
		if ((T321_1>1 || T321_2>1 || T321_3>1) && T321_4===1) {
			b.setRadioButton("T321_4", null);
		} 
		*/
		if (T321_1 > T321_4 || T321_2 > T321_4 || T321_3 > T321_4) {
			b.setRadioButton("T321_4", null);
		}
	}


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T321_4 = b.getValue("T321_4");
		if (!isNaN(T321_4) && T321_4 > 0) {
			result = 0;
			if (T321_4 === 1) {
				result = { b: 'A' };
			} else if (T321_4 === 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_2() {

	Bewertung.call(this);

	this.nr = "3.2.2";
	this.txt = "Strandberäumung";
	this.lrtCodes = [1210, 2110];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T322_1 = b.getValue("T322_1");

		if (!isNaN(T322_1) && T322_1 > 0) {
			result = 0;
			if (T322_1 === 1) {
				result = { b: 'A' };
			} else if (T322_1 === 2) {
				result = { b: 'B' };
			} else if (T322_1 === 3 || T322_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_3() {

	Bewertung.call(this);

	this.nr = "3.2.3";
	this.txt = "Stoffeinträge möglich durch fehlende Pufferstrukturen oberhalb der Kliffkante (außerhalb des LRT)";
	this.lrtCodes = [1230];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T323_1 = b.getValue("T323_1");

		if (!isNaN(T323_1) && T323_1 > 0) {
			result = 0;
			if (T323_1 === 1) {
				result = { b: 'A' };
			} else if (T323_1 === 2) {
				result = { b: 'B' };
			} else if (T323_1 === 3) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_4() {

	Bewertung.call(this);

	this.nr = "3.2.4";
	this.txt = "Mittlere Abstände zwischen Strandzugängen";
	this.lrtCodes = [1230, 2110, 2120, 2130, 2140, 2150, 2160, 2170];

	this.bewerte = function (b) {
		let result = -1;
		let lrtCode = b.lrtCode;
		let T324_1 = Number.parseInt(b.getValue("t324_1"));
		let T324_2 = b.getValue("T324_2");
		if (Number.isInteger(T324_2) && T324_2 > 0) {
			let nrEingabe = Number.isInteger(T324_1);
			let isValid = !nrEingabe;
			if (nrEingabe) {
				isValid = T324_2 === 3 && T324_1 < 150 || T324_2 === 2 && T324_1 <= 250 && T324_1 >= 150 || T324_2 === 1 && T324_1 > 250;
				result = result = { b: 0, err: "Eingebener Wert stimmt nicht mit dem gewählten Radio-Button überein." };
			}

			if (isValid) {
				result = 0;
				if (T324_2 === 1) {
					result = { b: 'A' };
				} else if (T324_2 === 2) {
					result = { b: 'B' };
				} else if (T324_2 === 3) {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_5() {

	Bewertung.call(this);

	this.nr = "3.2.5";
	this.txt = "Landwirtschaftliche Nutzung / Pflege";
	this.lrtCodes = [1330];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T325_1 = b.getValue("T325_1");

		if (!isNaN(T325_1) && T325_1 > 0) {
			result = 0;
			if (T325_1 === 1 || T325_1 === 2) {
				result = { b: 'A' };
			} else if (T325_1 === 3) {
				result = { b: 'B' };
			} else if (T325_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_6() {

	Bewertung.call(this);

	this.nr = "3.2.6";
	this.txt = "Deckung von Gehölzen (außer jeweils genannten Arten)";
	this.lrtCodes = [2130, 2140, 2150, 2160, 2170, 2190];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T326_1 = b.getValue("T326_1");

		if (!isNaN(T326_1) && T326_1 > 0) {
			result = 0;
			if (T326_1 === 1 || T326_1 === 2) {
				result = { b: 'A' };
			} else if (T326_1 === 3) {
				result = { b: 'B' };
			} else if (T326_1 === 4) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_8() {

	Bewertung.call(this);

	this.nr = "3.2.8";
	this.txt = "Anteil hochwüchsiger Gräser (z. B. Deschampsia flexuosa, Calamagrostis epigejos)";
	this.lrtCodes = [2150];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		// var T328_1_1 = b.getValue("T328_1_1");
		var T328 = parseInt(b.getValue("T328"));

		if (!isNaN(T328) && T328 >= 0) {
			result = 0;
			if (T328 < 10) {
				result = { b: 'A' };
			} else if (T328 >= 10 && T328 <= 30) {
				result = { b: 'B' };
			} else if (T328 > 30) {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungKueste_3_2_11() {

	Bewertung.call(this);

	this.nr = "3.2.11";
	this.txt = "Beeinträchtigung der natürlichen Dynamik (z. B. Küstenschutzmaßnahmen)";
	this.lrtCodes = [1210, 1220, 1310, 2110, 2120, 2130, 2140, 2150, 2160, 2170, 2190];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		var T3211_1, T3211_3, T3211_4;


		if ([1210, 1220, 1310].indexOf(lrtCode) >= 0) {

			T3211_1 = b.getValue("T3211_1");
			if (Number.isInteger(T3211_1) && T3211_1 > 0) {
				result = 0;
				if (T3211_1 === 1) {
					result = { b: 'A' };
				} else if (T3211_1 === 2) {
					result = { b: 'B' };
				} else if (T3211_1 === 3) {
					result = { b: 'C' };
				}
			}
		}
		else {
			if ([2110, 2120, 2130, 2140, 2150, 2160, 2170, 2190].indexOf(lrtCode) >= 0) {
				T3211_3 = parseInt(b.getValue("T3211_3"));
				T3211_4 = parseInt(b.getValue("T3211_4"));

				if (Number.isInteger(T3211_3) && Number.isInteger(T3211_4) && T3211_3 >= 0 && T3211_4 >= 0) {
					result = 0;
					if (T3211_3 === 0 && T3211_4 === 0) {
						result = { b: 'A' };
					} else if (T3211_3 === 2 || T3211_4 === 2) {
						result = { b: 'C' };
					} else if (T3211_3 === 1 || T3211_4 === 1) {
						result = { b: 'B' };
					}
				}
			}
		}
		this.result = result;
	}
}

// WALD

function bewertungWald_1_1_V1(b) {
	var result = -1;
	var result01 = -1;
	var result02 = -1;
	var T111_1 = b.getValue("T111");
	var T112_1 = b.getValue("T112");
	if (Number.isInteger(T111_1) && T111_1 >= 0 && Number.isInteger(T112_1) && T112_1 >= 0) {
		if (T111_1 < 7) {
			result01 = 1;
		} else if (T111_1 < 8) {
			result01 = 2;
		} else {
			result01 = 3;
		}
		if (T112_1 < 7) {
			result02 = 1;
		} else if (T112_1 < 8) {
			result02 = 2;
		} else {
			result02 = 3;
		}
		result = Math.max(result01, result02);
		if (result === 1) {
			result = { b: 'A' };
		} else if (result === 2) {
			result = { b: 'B' };
		} else {
			result = { b: 'C' };
		}
	}
	return result;
}
function bewertungWald_1_1_V2(b) {
	var result = -1;
	var T111 = b.getValue("T111");
	var T115 = b.getValue("T115");

	if (Number.isInteger(T111) && T111 >= 0 && Number.isInteger(T115) && T115 >= 0) {
		if (T115 === 1 && T111 < 8) {
			result = { b: 'A' };
		} else if (T115 <= 2 && T111 <= 8) {
			result = { b: 'B' };
		} else {
			result = { b: 'C' };
		}
	}
	return result;
}
/*
>=15% ja	>=10% ja	<10% nein
*/
function bewertungWald_1_1_V3(b) {
	var result = -1;
	var T111 = b.getValue("T111");
	var T114 = b.getValue("T114");
	console.error("T111=" + T111, "T114=" + T114);
	if (Number.isInteger(T111) && T111 >= 0 && Number.isInteger(T114) && T114 >= 0) {
		if (T114 === 1 && T111 <= 8) {
			result = { b: 'A' };
		} else if (T114 === 1 && T111 <= 9) {
			result = { b: 'B' };
		} else {
			result = { b: 'C' };
		}
	}
	return result;
}
function bewertungWald_1_1_V4(b) {
	var result = -1;
	var result = -1;
	var sum = 0;

	function isPhase(v) {
		var result = -1;
		if (Number.isInteger(v) && v >= 0) {
			result = (v < 10) ? 1 : 0;
		}
		return result;
	}

	var v1 = isPhase(b.getValue("T113_1"));
	var v2 = isPhase(b.getValue("T113_2"));
	var v3 = isPhase(b.getValue("T113_3"));
	var v4 = isPhase(b.getValue("T113_4"));
	var v5 = isPhase(b.getValue("T113_5"));
	console.info("vvvv", v1, v2, v3, v4, v5);

	if (v1 >= 0 && v2 >= 0 && v3 >= 0 && v4 >= 0 && v5 >= 0) {
		sum = v1 + v2 + v3 + v4 + v5;
		if (sum >= 3) {
			result = { b: 'A' };
		} else if (sum >= 2) {
			result = { b: 'B' };
		} else {
			result = { b: 'C' };
		}
	}
	return result;
}
function BewertungWald_1_1() {

	Bewertung.call(this);

	this.nr = "1.1.";
	this.txt = "Reifephase";
	this.lrtCodes = [9110, 9130, 9150, 9180, "91E0", 9160, 9190, "91G0"];

	this.bewerte = function (b) {
		console.info("BewertungWald_1_1.bewerte");
		var result = -1;
		var lrtCode = b.lrtCode;

		var lrtCodesVar01 = [9110, 9130, 9150];
		var lrtCodesVar02 = [9180];
		var lrtCodesVar03 = ["91E0"];
		var lrtCodesVar04 = [9160, 9190, "91G0"];

		if (lrtCodesVar01.indexOf(lrtCode) >= 0) {
			result = bewertungWald_1_1_V1(b);
		} else if (lrtCodesVar02.indexOf(lrtCode) >= 0) {
			result = bewertungWald_1_1_V2(b);
		} else if (lrtCodesVar03.indexOf(lrtCode) >= 0) {
			result = bewertungWald_1_1_V3(b);
		} else if (lrtCodesVar04.indexOf(lrtCode) >= 0) {
			result = bewertungWald_1_1_V4(b);
		}

		this.result = result;
	}

}
function aggrBewertung1_2(bwT211, bwT216) {
	const s = bwT211 + bwT216;
	switch (s) {
		case "AA":
			return "A"
		case "AB":
			return "A"
		case "AC":
			return "B"
		case "BA":
			return "B"
		case "BB":
			return "B"
		case "BC":
			return "B"
		case "CA":
			return "B"
		case "CB":
			return "C"
		case "CC":
			return "C"
		default:
			break;
	}
}

function BewertungWald_1_2() {

	Bewertung.call(this);

	this.nr = "1.2";
	this.txt = "Altbäume+Biotopbäume+Totholz oder Altholzinseln";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91E0", "91G0", "91T0", "91U0"];

	this.prepareHTML = function (b) {
		var targetId;
		targetId = getTextfieldId(b.layerId, b.datensatzNr, "t121_2_1");
		document.getElementById(targetId).disabled = true;

		targetId = getTextfieldId(b.layerId, b.datensatzNr, "t121_2_2");
		document.getElementById(targetId).disabled = true;
	}

	this.berechne = function (b) {
		console.info("berechne " + this.nr);
		const flaeche = b.getFlaecheInHa(b.layerId, b.datensatzNr);
		console.info("flaeche", flaeche);
		var T121_1_1 = parseInt(b.getValue("T121_1_1"));
		var T121_1_2 = parseInt(b.getValue("T121_1_2"));
		var T121_1_3 = parseInt(b.getValue("T121_1_3"));

		var sum01 = "";
		var targetId = getTextfieldId(b.layerId, b.datensatzNr, "t121_2_1");

		if (Number.isInteger(T121_1_1) && Number.isInteger(T121_1_2) && T121_1_1 >= 0 && T121_1_2 >= 0 && isFloat(flaeche)) {
			sum01 = T121_1_1 + T121_1_2;
			document.getElementById(targetId).value = sum01 / flaeche;
		} else {
			document.getElementById(targetId).value = "";
		}
		var sum02 = "";
		targetId = getTextfieldId(b.layerId, b.datensatzNr, "t121_2_2");
		if (Number.isInteger(T121_1_3) && T121_1_3 >= 0 && isFloat(flaeche)) {
			sum02 = T121_1_3;
			document.getElementById(targetId).value = sum02 / flaeche;
		} else {
			document.getElementById(targetId).value = "";
		}


	};


	this.bewerte = function (b) {
		console.info("BewertungWald_1_2.bewerte");
		// Summe 1.2.1 oder 1.2.2
		var result = -1;


		var T121_2_1 = parseFloat(b.getValue("T121_2_1"));
		var T121_2_2 = parseFloat(b.getValue("T121_2_2"));

		var T121_3 = parseInt(b.getValue("T121_3"));
		console.info("Sum01", T121_2_1);
		console.info("Sum02", T121_2_2);
		// 1: >=2%
		// 2: >=1%
		// 3: < 1%
		console.info("T121_3", T121_3, Number.isInteger(T121_3));
		if (isFloat(T121_2_1) && isFloat(T121_2_2) && Number.isInteger(T121_3) && T121_3 >= 0) {
			if (T121_2_1 >= 6 || T121_2_2 >= 6 || T121_3 === 1) {
				result = { b: 'A' };
			} else if (T121_2_1 >= 3 || T121_2_2 >= 3 || T121_3 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}


		this.result = result;
	}

}

function BewertungWald_1_3() {

	Bewertung.call(this);

	this.nr = "1.3.";
	this.txt = "Freiflächenanteil";
	this.lrtCodes = ["91T0", "91U0"];

	this.bewerte = function (b) {
		console.info("BewertungWald_1_3.bewerte");
		var result = -1;
		var T131 = parseInt(b.getValue("T131"));
		console.info("T131_1", T131);
		// >=20%	>=10%	<10%
		if (Number.isInteger(T131) && T131 >= 0) {
			if (T131 === 1) {
				result = { b: 'A' };
			} else if (T131 === 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}

}

function BewertungWald_1_4() {

	Bewertung.call(this);

	this.nr = "1.4.";
	this.txt = "Kronenschluß";
	this.lrtCodes = ["91T0", "91U0"];

	this.bewerte = function (b) {
		console.info("BewertungWald_1_3.bewerte");
		var result = -1;
		var T141 = parseInt(b.getValue("T141"));
		console.info("T141", T141);
		// <=30%	<=50%	>50%
		if (Number.isInteger(T141) && T141 >= 0) {
			if (T141 === 1) {
				result = { b: 'A' };
			} else if (T141 === 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}

}



function BewertungWald_1_5() {

	Bewertung.call(this);

	this.nr = "1.5";
	this.txt = "Vorkommen verschiedener Sukzessionsstadien";
	this.lrtCodes = [2180];

	this.prepareHTML = function (b) {
		b.setCalculated("t151_0");
	}


	this.berechne = function (b) {

		var T151_1 = b.getValue("T151_1");
		var targetId;
		targetId = getTextfieldId(b.layerId, b.datensatzNr, "t151_0");
		// var el = document.getElementById(targetId);
		b.setTextValue("t151_0", T151_1.length);


	}

	this.bewerte = function (b) {
		var result = -1;
		var T151_1 = parseInt(b.getValue("T151_0"));
		var T151_2 = parseInt(b.getValue("T151_2"));
		var T151_3 = parseInt(b.getValue("T151_3"));
		console.info("BewertungWald_1_5", T151_1, T151_2, T151_3);
		/*
		>=3    >=2	    1
		>10m	<=10m	nicht vorhanden
		<=10%	<=30%	>30%
		*/
		if (Number.isInteger(T151_1) && Number.isInteger(T151_2) && T151_2 >= 0 && Number.isInteger(T151_3) && T151_3 >= 0) {
			if (T151_1 >= 3 && T151_2 === 1 && T151_3 === 1) {
				result = { b: 'A' };
			} else if (T151_1 >= 2 && T151_2 <= 2 && T151_3 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}
		this.result = result;
	}
}

function BewertungWald_2_1() {

	Bewertung.call(this);

	this.nr = "2.1";
	this.txt = "Lebensraumtypisches Arteninventar - Pflanzenarten";
	this.lrtCodes = [9110, 9130, 9160, 9190, "91G0", "91U0", 2180, 9180, "91E0", 9150, "91D0", "91T0"];

	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;
		const lrtCodesVar01 = [9110, 9130, 9160, 9190, "91G0", "91U0", 2180];
		const lrtCodesVar02 = [9180, "91E0"];

		var T211 = parseInt(b.getValue("T211"));
		var T216 = parseInt(b.getValue("T216"));

		if (lrtCodesVar01.indexOf(lrtCode) >= 0) {
			console.info("BewertungWald_2_1 ", T211, T216);
			if (Number.isInteger(T211) && T211 >= 0 && Number.isInteger(T216) && T216 >= 0) {
				if (T211 <= 2 && T216 === 1) {
					result = { b: 'A' };
				} else if (T211 <= 3 && T216 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		} else if (lrtCodesVar02.indexOf(lrtCode) >= 0) {
			if (Number.isInteger(T211) && T211 >= 0 && Number.isInteger(T216) && T216 >= 0) {
				if (T211 <= 1 && T216 === 1) {
					result = { b: 'A' };
				} else if (T211 <= 2 && T216 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
			console.info("BewertungWald_2_1 ", T211, T216);
		} else if (lrtCode === 9150) {
			var T212 = parseInt(b.getValue("T212"));
			if (Number.isInteger(T211) && T211 > 0 && Number.isInteger(T212) && T212 > 0 && Number.isInteger(T216) && T216 > 0) {
				if (T211 <= 2 && T216 <= 1 && T212 <= 1) {
					result = { b: 'A' };
				} else if (T211 <= 3 && T216 <= 2 && T212 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
			console.info("BewertungWald_2_1 ", T211, T216, T212);
		} else if (lrtCode === "91D0") {
			var T213 = parseInt(b.getValue("T213"));
			if (Number.isInteger(T211) && T211 > 0 && Number.isInteger(T213) && T213 > 0 && Number.isInteger(T216) && T216 > 0) {
				if (T211 <= 1 && T216 <= 1 && T213 <= 1) {
					result = { b: 'A' };
				} else if (T211 <= 2 && T216 <= 2 && T213 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
			console.info("BewertungWald_2_1 ", T211, T216, T213);
		} else if (lrtCode === "91T0") {
			var T215 = parseInt(b.getValue("T215"));
			if (Number.isInteger(T211) && T211 > 0 && Number.isInteger(T215) && T215 > 0 && Number.isInteger(T216) && T216 > 0) {
				if (T211 <= 2 && T216 <= 1 && T215 <= 1) {
					result = { b: 'A' };
				} else if (T211 <= 3 && T216 <= 2 && T215 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
			console.info("BewertungWald_2_1 ", T211, T216, T215);
		}

		this.result = result;
	}

}

function BewertungWald_2_2() {

	Bewertung.call(this);

	this.nr = "2.2";
	this.txt = "Lebensraumtypisches Arteninventar - Pflanzen- und Tierarten";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91D0", "91E0", "91G0", "91T0", "91U0", 2180];


	this.getKorrekturWert = function () {
		return this.korrekturWert;
	}
	this.berechneKorrekturWert = function (b) {

		var T22_1 = b.getValue("T221");
		var T22_2 = b.getTextValue("T222");
		console.info("berechneKorrekturWert ", T22_1, T22_2);
		var korrekturWert;
		if (T22_1 === true) {
			if (T22_2 && T22_2.length > 3) {
				korrekturWert = 1;
			}
			else {
				korrekturWert = { err: 'Es muss mindestens eine Tierart angegeben werden.' };
			}
		}
		else {
			korrekturWert = 0;
		}
		this.korrekturWert = korrekturWert;
	}

}

function BewertungWald_3_1() {

	Bewertung.call(this);

	this.nr = "3.1";
	this.txt = "Beeinträchtigungen - Veränderung des Standorts";
	this.lrtCodes = [2180, 2180, 9160, 9190, "91D0", "91E0"];


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;

		if (lrtCode === 2180) {
			var T311 = parseInt(b.getValue("T311"));
			var T312 = parseInt(b.getValue("T312"));
			if (Number.isInteger(T311) && T311 > 0 && Number.isInteger(T312) && T312 > 0) {
				if (T311 === 1 && T312 === 1) {
					result = { b: 'A' };
				} else if (T311 <= 2 && T312 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		} else if (lrtCode === "91D0" || lrtCode === "91E0") {
			var T313 = parseInt(b.getValue("T313"));
			if (Number.isInteger(T313) && T313 > 0) {
				if (T313 === 1) {
					result = { b: 'A' };
				} else if (T313 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}

function BewertungWald_3_2() {

	Bewertung.call(this);

	this.nr = "3.2";
	this.txt = "Beeinträchtigungen - Gewässermorphologie";
	this.lrtCodes = ["91E0"];


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;

		if (lrtCode === "91E0") {
			var T32 = parseInt(b.getValue("T321"));
			console.info("BewertungWald_3_2", T32);
			if (Number.isInteger(T32) && T32 > 0) {
				if (T32 === 1) {
					result = { b: 'A' };
				} else if (T32 <= 2) {
					result = { b: 'B' };
				} else {
					result = { b: 'C' };
				}
			}
		}
		this.result = result;
	}

}


function BewertungWald_3_3() {

	Bewertung.call(this);

	this.nr = "3.3";
	this.txt = "Beeinträchtigungen - Fahrschäden";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91D0", "91E0", "91G0", "91T0", "91U0", 2180];


	this.bewerte = function (b) {
		var result = -1;
		var lrtCode = b.lrtCode;


		var T331 = parseInt(b.getValue("T331_1"));
		console.info("BewertungWald_3_3", T331);
		if (Number.isInteger(T331) && T331 > 0) {
			if (T331 === 1) {
				result = { b: 'A' };
			} else if (T331 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}

		this.result = result;
	}

}

function BewertungWald_3_4() {

	Bewertung.call(this);

	this.nr = "3.4";
	this.txt = "Beeinträchtigungen - Bodenbearbeitung";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91D0", "91E0", "91G0", "91T0", "91U0", 2180];


	this.bewerte = function (b) {
		var result = -1;

		var T341 = parseInt(b.getValue("T341"));
		console.info("BewertungWald_3_4", T341);
		if (Number.isInteger(T341) && T341 > 0) {
			if (T341 === 1) {
				result = { b: 'A' };
			} else if (T341 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}

		this.result = result;
	}

}

function BewertungWald_3_5() {

	Bewertung.call(this);

	this.nr = "3.5";
	this.txt = "Beeinträchtigungen - Schäden an Waldvegetation";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91D0", "91E0", "91G0", "91T0", "91U0", 2180];


	this.bewerte = function (b) {
		var result = -1;

		var T351 = parseInt(b.getValue("T351"));
		console.info("BewertungWald_3_5", T351);
		if (Number.isInteger(T351) && T351 > 0) {
			if (T351 === 1) {
				result = { b: 'A' };
			} else if (T351 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}

		this.result = result;
	}
}

function BewertungWald_3_6() {

	Bewertung.call(this);

	this.nr = "3.6";
	this.txt = "Beeinträchtigungen - Aufforstung von Freiflächenn";
	this.lrtCodes = ["91T0", "91U0"];


	this.bewerte = function (b) {
		var result = -1;

		var T361 = parseInt(b.getValue("T361_1"));
		var T362 = parseInt(b.getValue("T361_2"));
		console.info("BewertungWald_3_6", T361, T362);
		if (Number.isInteger(T361) && T361 > 0 && Number.isInteger(T362) && T362 > 0) {
			if (T361 === 1 && T362 === 1) {
				result = { b: 'A' };
			} else if (T361 <= 2 && T362 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}

		this.result = result;
	}
}

function BewertungWald_3_7() {

	Bewertung.call(this);

	this.nr = "3.7";
	this.txt = "Beeinträchtigungen - Weitere (gutachterlich)";
	this.lrtCodes = [9110, 9130, 9150, 9160, 9180, 9190, "91D0", "91E0", "91G0", "91T0", "91U0", 2180];


	this.bewerte = function (b) {
		var result = -1;

		var T371 = parseInt(b.getValue("T371_2"));
		console.info("BewertungWald_3_7", T371);
		if (Number.isInteger(T371) && T371 > 0) {
			if (T371 === 1) {
				result = { b: 'A' };
			} else if (T371 <= 2) {
				result = { b: 'B' };
			} else {
				result = { b: 'C' };
			}
		}

		this.result = result;
	}

}

var bewertungenKueste = [
	new BewertungKueste_1_1_1(),
	new BewertungKueste_1_1_2(),
	new BewertungKueste_1_1_3(),
	new BewertungKueste_1_2_1(),
	new BewertungKueste_1_3_1(),
	new BewertungKueste_1_4_1(),
	new BewertungKueste_1_4_2(),
	new BewertungKueste_1_4_3(),
	new BewertungKueste_1_4_4(),
	new BewertungKueste_1_4_5(),

	new BewertungKueste_1_5_1(),
	new BewertungKueste_1_5_2(),
	new BewertungKueste_1_5_3(),
	new BewertungKueste_1_5_4(),
	new BewertungKueste_1_5_5(),
	new BewertungKueste_1_5_6(),

	new BewertungKueste_2_1_1(),
	new BewertungKueste_2_2(),
	new BewertungKueste_3_1_1(),
	new BewertungKueste_3_2_1(),
	new BewertungKueste_3_2_2(),
	new BewertungKueste_3_2_3(),
	new BewertungKueste_3_2_4(),
	new BewertungKueste_3_2_5(),
	new BewertungKueste_3_2_6(),
	new BewertungKueste_3_2_8(),
	new BewertungKueste_3_2_11()
];

var bewertungenFliessgewaesser = [
	new BewertungFliessgewaesser_1_1_1(),
	new BewertungFliessgewaesser_1_2_1(),
	new BewertungFliessgewaesser_2_1_1(),
	new BewertungFliessgewaesser_2_2(),
	new BewertungFliessgewaesser_2_2_a(),
	new BewertungFliessgewaesser_2_2_b(),
	new BewertungFliessgewaesser_2_2_c(),
	new BewertungFliessgewaesser_3_1_1(),
	new BewertungFliessgewaesser_3_1_2(),
	new BewertungFliessgewaesser_3_1_3(),
	new BewertungFliessgewaesser_3_2_1(),
	new BewertungFliessgewaesser_3_2_2(),
	new BewertungFliessgewaesser_3_3_1(),
	new BewertungFliessgewaesser_3_3_2(),
	new BewertungFliessgewaesser_3_4_1()
];


var bewertungenStillgewaesser = [
	new BewertungStillgewaesser_1_1_1(),
	new BewertungStillgewaesser_1_1_2(),
	new BewertungStillgewaesser_1_1_3(),
	new BewertungStillgewaesser_1_2_1(),
	new BewertungStillgewaesser_2_1_1(),
	new BewertungStillgewaesser_2_1_1_a(),
	new BewertungStillgewaesser_2_1_2(),
	new BewertungStillgewaesser_2_2(),
	new BewertungStillgewaesser_2_2_a(),
	new BewertungStillgewaesser_3_1_1(),
	// new BewertungStillgewaesser_3_1_1_a(),
	// new BewertungStillgewaesser_3_1_2b(),
	new BewertungStillgewaesser_3_1_2(),
	new BewertungStillgewaesser_3_1_3(),
	new BewertungStillgewaesser_3_1_5(),
	new BewertungStillgewaesser_3_1_7(),
	new BewertungStillgewaesser_3_2_1(),
	new BewertungStillgewaesser_3_2_2()
];

var bewertungenOffenland = [
	new BewertungOffenland_1_1_1(),
	new BewertungOffenland_1_1_2(),
	new BewertungOffenland_1_1_3(),
	new BewertungOffenland_1_2_1(),
	new BewertungOffenland_1_3_1(),
	new BewertungOffenland_1_3_2(),
	new BewertungOffenland_2_1_1(),
	new BewertungOffenland_2_1_2(),
	new BewertungOffenland_2_2(),
	new BewertungOffenland_3_1_1(),
	new BewertungOffenland_3_2_1(),
	new BewertungOffenland_3_2_2(),
	new BewertungOffenland_3_2_3(),
	new BewertungOffenland_3_2_4(),
	new BewertungOffenland_3_2_5(),
	new BewertungOffenland_3_2_6(),
	new BewertungOffenland_3_2_7(),
	new BewertungOffenland_3_2_8()
];

var bewertungenMoore = [
	new BewertungMoore_1_1_1(),
	new BewertungMoore_1_1_2(),
	new BewertungMoore_1_1_3(),
	new BewertungMoore_1_2_1(),
	new BewertungMoore_1_2_2(),
	new BewertungMoore_1_3_1(),
	new BewertungMoore_1_3_2(),
	new BewertungMoore_2_1_1(),
	new BewertungMoore_2_2(),
	new BewertungMoore_3_1_1(),
	new BewertungMoore_3_2_1(),
	new BewertungMoore_3_2_2(),
	new BewertungMoore_3_2_3(),
	new BewertungMoore_3_2_4(),
	new BewertungMoore_3_2_5(),
	new BewertungMoore_3_2_8()
];

var bewertungenWald = [
	new BewertungWald_1_1(),
	new BewertungWald_1_2(),
	new BewertungWald_1_3(),
	new BewertungWald_1_4(),
	new BewertungWald_1_5(),
	new BewertungWald_2_1(),
	new BewertungWald_2_2(),
	new BewertungWald_3_1(),
	new BewertungWald_3_2(),
	new BewertungWald_3_3(),
	new BewertungWald_3_4(),
	new BewertungWald_3_5(),
	new BewertungWald_3_6(),
	new BewertungWald_3_7(),
];

function NebenCodeRecord(code, name, percentage) {
	this.code = code;
	this.name = name;
	this.percentage = percentage;

	this.toString = function () {
		return this.code + ", " + this.name + ", " + this.percentage + "%";
	}
}

function LrtBewertung(dsBewertung, bewertungen) {


	this.dsBewertung = dsBewertung;

	this.bewertungen = [];

	var gesamtBewertung = new GesamtBewertung(dsBewertung);

	this.gesamtBewertung = gesamtBewertung;

	var grpNr;
	var gruppenBewertung;

	for (var i = 0; i < bewertungen.length; i++) {
		var bewertung = bewertungen[i];
		if (bewertung.isRequired(this.dsBewertung)) {
			if (!gruppenBewertung) {
				grpNr = parseInt(bewertung.nr.substring(0, bewertung.nr.indexOf('.')));
				gruppenBewertung = new Gruppenbewertung(dsBewertung, grpNr);
				gesamtBewertung.addBewertung(gruppenBewertung);
			}
			else {
				grpNr = parseInt(bewertung.nr.substring(0, bewertung.nr.indexOf('.')));
				if (grpNr !== gruppenBewertung.nr) {
					this.bewertungen.push(gruppenBewertung);
					gruppenBewertung = new Gruppenbewertung(dsBewertung, grpNr);
					gesamtBewertung.addBewertung(gruppenBewertung);
				}
			}
			this.bewertungen.push(bewertung);
			gruppenBewertung.addBewertung(bewertung);
		}
		else {
			//			console.info("B: "+bewertung.nr+"  required==false "+this.dsBewertung.lrtCode);
		}

	}
	if (gruppenBewertung) {
		this.bewertungen.push(gruppenBewertung);
	}
	this.bewertungen.push(gesamtBewertung);


	this.getBewertungen = function () {
		return this.bewertungen;
	};

	this.validate = function (evt) {
		let changedByUser = this.gesamtBewertung.changedByUser();
		let begruendung = this.dsBewertung.getValue("bemerkung");
		if (changedByUser && (!begruendung || begruendung.length <= 5)) {
			alert("Sie haben die vom System ermittelte Bewertung geändert.\nBitte tragen Sie eine Begründung ein");
			return false;
		}
		return true;
	}

}

function Bewertung() {
	this.result = -1;
	this.getResult = function () {
		return this.result;
	}
	this.isRequired = function (b) {
		const isRequired = this.lrtCodes.indexOf(b.lrtCode) >= 0;
		console.error("isRequired " + this.nr + "required=" + isRequired, isRequired, b, b.lrtCode, this.lrtCodes.indexOf(b.lrtCode));
		return isRequired;
	}
}

function GesamtBewertung(dsBewertung) {


	Bewertung.call(this);

	this.msgChangedByUser = "Bewertung wurde durch den Bearbeiter geändert.";

	this.nr = "Gesamt";
	// A 156_sys_erhalt_0_0
	// B 156_sys_erhalt_1_0
	// C 156_sys_erhalt_2_0
	this.inputTargetA = document.getElementById(dsBewertung.layerId + "_sys_erhalt_0_" + dsBewertung.datensatzNr);
	this.inputTargetB = document.getElementById(dsBewertung.layerId + "_sys_erhalt_1_" + dsBewertung.datensatzNr);
	this.inputTargetC = document.getElementById(dsBewertung.layerId + "_sys_erhalt_2_" + dsBewertung.datensatzNr);
	this.inputTargetUserA = document.getElementById(dsBewertung.layerId + "_bea_erhalt_0_" + dsBewertung.datensatzNr);
	this.inputTargetUserB = document.getElementById(dsBewertung.layerId + "_bea_erhalt_1_" + dsBewertung.datensatzNr);
	this.inputTargetUserC = document.getElementById(dsBewertung.layerId + "_bea_erhalt_2_" + dsBewertung.datensatzNr);

	this.inputTargetA.readOnly = true;
	this.inputTargetB.readOnly = true;
	this.inputTargetC.readOnly = true;
	this.inputTargetUserA.readOnly = true;
	this.inputTargetUserB.readOnly = true;
	this.inputTargetUserC.readOnly = true;

	// Ausblenden der Zeile Habitatstrukturen, da bei Lrt 91D0 nicht vorhanden
	if (dsBewertung.lrtCode === "91D0") {
		const el = document.getElementById(dsBewertung.layerId + "_sys_habit_0_" + dsBewertung.datensatzNr);
		el.parentElement.parentElement.style.display = 'none';
	}


	this.inputBemerkung = document.getElementById(dsBewertung.layerId + "_bemerkung_" + dsBewertung.datensatzNr);

	this.gruppenBewertungen = [];

	// zentrieren
	this.inputTargetA.parentNode.style.textAlign = 'center';
	this.inputTargetUserA.parentNode.style.textAlign = 'center';



	this.bewerte = function (b) {
		var i, result, resultSys, resultBea, grResultSystem = [], grResultBearbeiter = [];

		var bewTxt = this.gruppenBewertungen[0].getResult().b;
		for (i = 1; i < this.gruppenBewertungen.length; i++) {
			bewTxt += ", " + this.gruppenBewertungen[i].getResult().b;
		}

		console.info("Gesambewertung " + b.lrtCode + "  AnzGruppenBewertungen=" + this.gruppenBewertungen.length + "  bewertungen=" + bewTxt);


		// Weil keine Habitatstruktur wird bei LRT 91D0 Arteninventar doppelt einbezogen
		if (dsBewertung.lrtCode === "91D0") {
			console.info("fddffdfdfd");
			grResultSystem.push(this.gruppenBewertungen[0].getResult().b);
			grResultBearbeiter.push(this.gruppenBewertungen[0].getUserResult().b);
		}


		for (i = 0; i < this.gruppenBewertungen.length; i++) {
			grResultSystem.push(this.gruppenBewertungen[i].getResult().b);
			grResultBearbeiter.push(this.gruppenBewertungen[i].getUserResult().b);
		}



		resultSys = getGesamtBewertung(grResultSystem);
		if (resultSys) {
			this.result = { b: resultSys };
			this.inputTargetA.checked = 'A' === resultSys;
			this.inputTargetB.checked = 'B' === resultSys;
			this.inputTargetC.checked = 'C' === resultSys;
		}
		else {
			this.result = -1;
			this.inputTargetA.checked = false;
			this.inputTargetB.checked = false;
			this.inputTargetC.checked = false;
		}

		resultBea = getGesamtBewertung(grResultBearbeiter);
		if (resultBea) {
			this.resultBea = { b: resultBea };
			this.inputTargetUserA.checked = 'A' === resultBea;
			this.inputTargetUserB.checked = 'B' === resultBea;
			this.inputTargetUserC.checked = 'C' === resultBea;
		}
		else {
			this.resultBea = -1;
			this.inputTargetUserA.checked = false;
			this.inputTargetUserB.checked = false;
			this.inputTargetUserC.checked = false;
		}

		/*
		if (this.changedByUser()) {			
			let idx = this.inputBemerkung.value.indexOf(this.msgChangedByUser);
			if (idx<0) {
				this.inputBemerkung.value = this.msgChangedByUser+" "+this.inputBemerkung.value;
			}
		}
		else {			
			let idx = this.inputBemerkung.value.indexOf(this.msgChangedByUser);
			if (idx>=0) {
				let txt = this.inputBemerkung.value.replace(this.msgChangedByUser, "").trim();
				this.inputBemerkung.value = txt;
			}
		}
		*/
	}

	this.changedByUser = function () {
		for (i = 0; i < this.gruppenBewertungen.length; i++) {
			if (this.gruppenBewertungen[i].changedByUser()) {
				return true;
			}
		}
		return false;
	}

	this.addBewertung = function (bewertung) {
		this.gruppenBewertungen.push(bewertung);
	}

}

function Gruppenbewertung(dsBewertung, grpNr) {

	Bewertung.call(this);

	this.nr = grpNr;
	this.teilBewertungen = [];
	this.bewertungen = [];
	this.korrekturen = [];

	this.changedByUser;

	var inpNames = ["sys_habit", "sys_leben", "sys_beein"];
	var inpNamesUser = ["bea_habit", "bea_leben", "bea_beein"];
	// this.inputTarget = dsBewertung.layerId+"_"+inpNames[grpNr-1]+"_"+dsBewertung.datensatzNr;
	this.inputTargetA = document.getElementById(dsBewertung.layerId + "_" + inpNames[grpNr - 1] + "_0_" + dsBewertung.datensatzNr);
	this.inputTargetB = document.getElementById(dsBewertung.layerId + "_" + inpNames[grpNr - 1] + "_1_" + dsBewertung.datensatzNr);
	this.inputTargetC = document.getElementById(dsBewertung.layerId + "_" + inpNames[grpNr - 1] + "_2_" + dsBewertung.datensatzNr);
	this.inputTargetUserA = document.getElementById(dsBewertung.layerId + "_" + inpNamesUser[grpNr - 1] + "_0_" + dsBewertung.datensatzNr);
	this.inputTargetUserB = document.getElementById(dsBewertung.layerId + "_" + inpNamesUser[grpNr - 1] + "_1_" + dsBewertung.datensatzNr);
	this.inputTargetUserC = document.getElementById(dsBewertung.layerId + "_" + inpNamesUser[grpNr - 1] + "_2_" + dsBewertung.datensatzNr);

	// zentrieren
	// zentrieren
	this.inputTargetA.parentNode.style.textAlign = 'center';
	this.inputTargetUserA.parentNode.style.textAlign = 'center';

	this.bewerteWald = function (b) {

		var result;

		const arrBewertungen = [];
		var s = "";
		for (i = 0; i < this.bewertungen.length; i++) {
			if (this.bewertungen[i].bewerte) {
				const b = this.bewertungen[i].getResult().b;
				s += b || "?";
			} else {
				s += "K";
			}
		}
		console.info("Gruppenbewertung.bewerteWald nr=" + this.nr + " AnzTeilBewertungen=" + this.bewertungen.length, s, this);

		for (i = 0; i < this.bewertungen.length; i++) {
			if (this.bewertungen[i].bewerte) {
				var b = this.bewertungen[i].getResult().b;
				if (!b) {
					return;
				}
				arrBewertungen.push(this.bewertungen[i].getResult().b);
			}
		}


		if (this.nr === 1) {
			if (this.bewertungen.length === 3) {
				result = getGesamtBewertung(arrBewertungen);
			} else if (this.bewertungen.length === 2) {
				var v1 = arrBewertungen[0];
				var v2 = arrBewertungen[1];
				result = aggrBewertung1_2(v1, v2);
			} else if (this.bewertungen.length === 1) {
				return arrBewertungen[0];
			} else {
				console.error("nicht erwartet");
			}
		} else if (this.nr === 2) {
			if (arrBewertungen.length === 1) {
				return arrBewertungen[0];
			}
			result = getGesamtBewertung(arrBewertungen);

		} else if (this.nr === 3) {
			var cResult = "A";
			for (i = 0; i < arrBewertungen.length; i++) {
				let bw = arrBewertungen[i];
				if (bw === "C") {
					cResult = "C";
					break;
				}
				if (bw === "B" && cResult === "A") {
					cResult = "B";
				}
			}
			result = cResult;
		}
		return result;

	}
	this.bewerte = function (b) {
		var result;
		var korrekturen = 0;
		if (b.isWald()) {
			result = this.bewerteWald(b);
			console.info("hggvjgj", result)
		} else {
			this.bewertungen = [];
			this.korrekturen = [];
			for (var i = 0; i < this.teilBewertungen.length; i++) {
				if (this.teilBewertungen[i].bewerte) {
					this.bewertungen.push(this.teilBewertungen[i]);
				}
				if (this.teilBewertungen[i].getKorrekturWert) {
					this.korrekturen.push(this.teilBewertungen[i]);
				}
			}
			for (var i = 0; i < this.bewertungen.length; i++) {
				if (this.bewertungen[i].bewerte) {
					subResult = this.bewertungen[i].getResult();
					// console.info(this.bewertungen[i].nr+"  "+subResult);
					if (!subResult.skiped) {
						if (subResult.b) {
							if (result) {
								if (subResult.b > result) {
									result = subResult.b;
								}
							}
							else {
								result = subResult.b;
							}
						}
						else {
							result = null;
							break;
						}
					}
				}
			}
		}
		for (var i = 0; i < this.korrekturen.length; i++) {
			if (this.korrekturen[i].getKorrekturWert) {
				let korrekturWert = this.korrekturen[i].getKorrekturWert();
				// console.info("rrr01 ", this.nr, korrekturWert, isNaN(korrekturWert));
				if (Number.isInteger(korrekturWert)) {
					korrekturen += this.korrekturen[i].getKorrekturWert(b);
				}
				else {
					// console.info("rrr isNaN",b, this.korrekturen[i].getKorrekturWert());
					result = null;
					this.korrekturen[i].result = -1;
					break;
				}
			}
		}
		// console.info("Gruppenbewertung.bewrte result", result);
		if (result) {

			resultKorrigiert = this.korrigiere(result, korrekturen);

			this.result = { b: resultKorrigiert };

			if (this.korrekturen.length === 1) {
				this.korrekturen[0].result = result + " &#8594; " + resultKorrigiert;
				this.korrekturen[0].resultKorrigiert = resultKorrigiert;
			}
			this.inputTargetA.checked = 'A' === resultKorrigiert;
			this.inputTargetB.checked = 'B' === resultKorrigiert;
			this.inputTargetC.checked = 'C' === resultKorrigiert;
		}
		else {
			this.result = -1;
			if (this.korrekturen.length === 1) {
				this.korrekturen[0].result = -1;
				this.korrekturen[0].resultKorrigiert = null;
			}
			this.inputTargetA.checked = false;
			this.inputTargetB.checked = false;
			this.inputTargetC.checked = false;
		}

	}

	this.getUserResult = function () {
		if (this.inputTargetUserA.checked === true) {
			return { b: 'A' };
		}
		if (this.inputTargetUserB.checked === true) {
			return { b: 'B' };
		}
		if (this.inputTargetUserC.checked === true) {
			return { b: 'C' };
		}
		return this.getResult();
	}

	this.changedByUser = function () {
		var resultBea = this.getUserResult();
		var resultSys = this.getResult();
		// console.info("", resultBea, resultSys);
		if (resultBea === resultSys) {
			return false;
		}
		if (resultBea.b && resultSys.b) {
			return resultBea.b !== resultSys.b;
		}
		return true;
	}

	this.korrigiere = function (result, korrekturen) {
		if (isNaN(korrekturen)) {
			result = null;
		}
		else {
			if (korrekturen < 0) {
				if (result === 'A') {
					return 'B';
				} else {
					return 'C';
				}
			}
			if (korrekturen > 0) {
				if (result === 'C') {
					return 'B';
				} else {
					return 'A';
				}
			}
		}
		return result;
	}




	this.addBewertung = function (bewertung) {
		this.teilBewertungen.push(bewertung);
		if (bewertung.bewerte) {
			this.bewertungen.push(bewertung);
		}
		if (bewertung.getKorrekturWert) {
			this.korrekturen.push(bewertung);
		}
	}
}
/**
 * 
 * @param {*} layerId 
 * @param {*} datensatzNr 
 */
function DatensatzBewertung(layerId, datensatzNr) {
	this.datensatzNr = datensatzNr;
	this.layerId = layerId;

	this.map = [];

	this.bewertung;

	this.frame = null;

	this.lrtCode;

	this.habitate = null;
	this.gefcodes = null;
	this.nebencodes = null;


	this.start = function () {
		console.info("start layerId=" + this.layerId + " datensatzNr=" + this.datensatzNr, this);
		if (this.layerId === 109) {
		}
		this.lrtCode = this._getLRTCode();
		if (this.layerId === 144) {
			this.bewertung = new LrtBewertung(this, bewertungenStillgewaesser);
			this.addListener50ha();
		} else if (this.layerId === 145) {
			this.bewertung = new LrtBewertung(this, bewertungenFliessgewaesser);
		} else if (this.layerId === 156) {
			this.bewertung = new LrtBewertung(this, bewertungenKueste);
		} else if (this.layerId === 157) {
			this.bewertung = new LrtBewertung(this, bewertungenMoore);
		} else if (this.layerId === 160) {
			this.bewertung = new LrtBewertung(this, bewertungenOffenland);
		} else if (this.layerId === 431) {
			this.bewertung = new LrtBewertung(this, bewertungenWald, true);
		} else {
			// unknown not the right Layer
			console.info("skip " + this.layerId);
			return;
		}

		//		this.hide(this.layerId+"_group_Stammdaten");
		this.hide(this.layerId + "_group_Schutzmerkmale");
		this.hide(this.layerId + "_group_Angaben_zur_Erstkartierung");
		this.hide(this.layerId + "_group_FFH-LRT");
		this.hide(this.layerId + "_group_Biotoptypen-LRT");
		//		this.hide(this.layerId+"_group_Biotoptypen");
		this.hide(this.layerId + "_group_Beschreibung");
		this.hide(this.layerId + "_group_Bearbeitung");
		this.hide(this.layerId + "_group_Fotos");
		this.hide(this.layerId + "_group_Rest");

		document.getElementById("dataset_operations").style.display = 'none';

		if (this.bewertung) {
			this.frame = new BewertungFrame(this.datensatzNr, this.bewertung);
			this.createExtraColumns();

			// this.prepareHTML();
			// this.bewerte();

		}
		else {
			alert("keine Bewertungen für LayerId=\"" + this.layerId + "\"");
		}

		const self = this;
		const bSelf = this;
		// console.error("root.open_subform_requests="+root.open_subform_requests, this, self);
		if (root.open_subform_requests === 0) {
			this.prepareHTML();
			this.bewerte();
			this.submit();
		}
		else {
			// const self = this;


			let callback = function (evt) {
				// console.error("MutationObserver root.open_subform_requests="+root.open_subform_requests, evt);
				console.info("DatensatzBewertung callback " + root.open_subform_requests);
				if (root.open_subform_requests === 0) {
					self.readHabitate();
					self.readGefaehrdungen();
					self.readNebencodes();
					if (self.habitate && self.nebencodes) {
						if (self.gefcodes || self.layerId === 144 || self.layerId === 145 || self.layerId === 160) {
							self.prepareHTML();
							self.bewerte();
						} else {
							console.error("gefcodes are null");
						}
					} else {
						console.error("sth is null habitate or nebencodes", self.habitate, self.nebencodes);
						console.info("scope", self);
					};
					console.info("DatensatzBewertung initialisiert");
					self.submit();
				}
			}
			const intervalID = setInterval(() => {
				if (root.open_subform_requests === 0) {
					callback();
					clearInterval(intervalID);
				}
			}, "500");

			/*
			var config = { attributes: true, childList: true, subtree: true };
			let observer = new MutationObserver(callback);
			let elem = document.getElementById(this.layerId+"_habitate_"+this.datensatzNr);			
			if (elem && elem.parentNode) {
				console.info("_habitate_");
				observer.observe(elem.parentNode, config);
			}
			elem = document.getElementById(this.layerId+"_gefcode_"+this.datensatzNr);
			if (elem && elem.parentNode) {
				console.info("_gefcode_");
				observer.observe(elem.parentNode, config);
			}
			elem = document.getElementById(this.layerId+"_nc_"+this.datensatzNr);
			if (elem && elem.parentNode) {
				console.info("_nc_");
				observer.observe(elem.parentNode, config);
			}
			elem = document.getElementById(this.layerId+"_fotos_"+this.datensatzNr);
			if (elem && elem.parentNode) {
				console.info("_fotos_");
				observer.observe(elem.parentNode, config);
			}
			*/
		}

		this.printHTML();

	}

	this.readHabitate = function () {

		let e = document.getElementById(this.layerId + "_habitate_" + this.datensatzNr).parentNode;
		console.info(e);
		var elements = e.getElementsByClassName("subFormListItem");

		this.habitate = [];

		var i, count = elements.length;
		var f;
		for (i = 0; i < count; i++) {
			f = elements[i];
			var a = f.getElementsByTagName("a");
			if (a.length > 0) {
				let text = a[0].innerText;
				let codeHabitat = text.split(" ")[0];
				if (codeHabitat) {
					this.habitate.push(codeHabitat);
				}
			}
		}
		// this.bewerte();
	}

	this.readGefaehrdungen = function () {
		// console.info("readGefaehrdungen");

		let e = document.getElementById(this.layerId + "_gefcode_" + this.datensatzNr);
		if (e && e.parentNode) {
			var elements = e.parentNode.getElementsByClassName("subFormListItem");

			this.gefcodes = [];

			// console.info("rrrrrr ",elements);
			var i, count = elements.length;
			var f;
			for (i = 0; i < count; i++) {
				f = elements[i];
				var a = f.getElementsByTagName("a");
				if (a.length > 0) {
					let text = a[0].innerText;
					let codeGef = text.split(" ")[0];
					if (codeGef) {
						this.gefcodes.push(codeGef);
					}
				}
			}
			// this.bewerte();
		}
	}

	this.readNebencodes = function () {

		try {
			const results = [];
			let s = this.layerId + "_nc_" + this.datensatzNr;
			let e = document.getElementById(s);
			let elements = e.getElementsByClassName("subFormListItem");
			let count = elements.length;
			let f;
			for (let i = 0; i < count; i++) {
				f = elements[i];
				let a = f.getElementsByTagName("a");
				let sA = a[0].innerText.split(" ");
				let code = sA[0]
				let name = "";
				for (let aNr = 0; aNr < sA.length - 2; aNr++) {
					name += sA[aNr];
				}
				let percentage = Number(sA[sA.length - 2]);
				results.push(new NebenCodeRecord(code, name, percentage));
			}
			this.nebencodes = results;
		} catch (e) {
			console.error(e);
		}
	}


	this.isVisible = function (elem) {
		return elem.offsetHeight > 0;
	}

	this.printHTML = function () {
		// console.info("printHTML");
		var data = [];
		for (k in this.map) {
			if (k.length > 1) {
				data.push(k);
			}
		}
		var s = "LRTCode=" + this._getLRTCode();
		var i = 0;
		for (i = 0, count = data.length; i < count; i++) {
			if (i >= 0) { s += '\n'; }
			k = data[i];
			s += k + " " + this.isVisible(this.map[k][0]);
		}
		// console.info(s);
	}

	this.hasHabitat = function (habitat) {
		if (this.habitate && this.habitate.length > 0) {
			for (let i = 0, c = this.habitate.length; i < c; i++) {
				if (habitat === this.habitate[i]) {
					return true;
				}
			}
		}
		return false;
	}

	this.hasGefcode = function (gefcode) {
		if (this.gefcodes && this.gefcodes.length > 0) {
			for (let i = 0, c = this.gefcodes.length; i < c; i++) {
				if (gefcode === this.gefcodes[i]) {
					return true;
				}
			}
		}
		return false;
	}

	this.getBewertung = function (bewertungsNr) {
		var i;
		var bewertungen = this.bewertung.getBewertungen();
		for (i = 0; i < bewertungen.length; i++) {
			if (bewertungen[i].nr === bewertungsNr) {
				return bewertungen[i];
			}
		}
	}

	this.hide = function (className) {
		console.error("hide", className);
		let elems = document.getElementsByClassName(className);
		if (elems) {
			for (let i = 0; i < elems.length; i++) {
				elems[i].style.display = 'none';
				elems[i].className = elems[i].className + " display_none";
			}
		}

	}


	var getParent = function (child, parentTag) {
		var parent;
		if (child.parentNode) {
			parent = child.parentNode;
			if (parent.tagName === parentTag) {
				return parent;
			}
			return getParent(parent, parentTag);
		}
	}

	var getNextSiblingWithTag = function (node, tagName) {
		var nextSibling;
		if (node.nextSibling) {
			nextSibling = node.nextSibling;
			// console.info(parent.tagName);
			if (nextSibling.tagName === tagName) {
				return nextSibling;
			}
			return getNextSiblingWithTag(nextSibling, tagName);
		}
	}

	this.setVisibiltyOfBlock = function (s, visible) {
		var i = 0;
		var matches = document.body.querySelectorAll('table.tgle');
		var dsBase = matches[this.datensatzNr];
		matches = dsBase.querySelectorAll('span.fett');
		for (i = 0; i < matches.length; i++) {
			if (matches[i].innerText.indexOf(s) === 0) {
				let table01 = getParent(matches[i], "TABLE");
				if (visible) {
					table01.parentNode.parentNode.style.display = "";
				} else {
					table01.parentNode.parentNode.style.display = "none";
				}
				return;
			}
		}
	}

	this.createExtraColumns = function () {
		console.error("createExtraColumns", this);
		if (layerId == 431) {
			// return
		}
		var i = 0;
		var matches = document.body.querySelectorAll('table.tgle');
		var dsBase = matches[this.datensatzNr];
		matches = dsBase.querySelectorAll('span.fett');
		for (i = 0; i < matches.length; i++) {
			var s = matches[i].innerText.substring(0, 10);
			if (s.lastIndexOf('.') > 0) {
				s = s.substring(0, s.indexOf(' '));
				var bewertung = this.getBewertung(s);
				var isRequired = (bewertung && bewertung.isRequired(this));
				if (this.isWald()) {
					isRequired = true;
				}
				if (isRequired) {
					var table01 = getParent(matches[i], "TABLE");
					var table02 = getNextSiblingWithTag(table01, "TABLE");
					// table02.style.tableLayout="fixed";
					if (table02 && table02.rows && table02.rows.length > 0) {

						var row = table02.rows[0];
						if (!row) {
							console.error("Fehler bei \"" + s + "\" " + table02.rows.length);
						}
						if (row.style.display === 'none') {
							row.style.display = 'table-row';
						}
						var cell = row.insertCell(row.cells.length);
						cell.rowSpan = table02.rows.length;
						cell.width = "30px";
						cell.style.minWidth = "30px";

						// cell.style.width="30px";
						// cell.style.minWidth="30px";
						// cell.style.maxWidth="30px";

						cell.style.backgroundColor = "gray";
						cell.style.textAlign = "center";

						if (bewertung) {
							cell.innerText = bewertung.nr;

							// bewertung.cell=cell;
							/*
							innerDiv.style.backgroundColor="gray";
							innerDiv.style.textAlign="center";
							innerDiv.rowSpan=table02.rows.length;
							innerDiv.innerText=bewertung.nr;
							bewertung.cell=innerDiv;
							*/
							bewertung.cell = cell;
						}
					}
					else {
						console.error("Fehler bei \"" + s + "\"");
					}
				}
				else {

					if (s.length > 2 && layerId !== 1431) {
						let table01 = getParent(matches[i], "TABLE");
						table01.parentNode.parentNode.style.display = "none";
					}
				}
			}
		}
	}

	this.prepareHTML = function () {
		if (this.bewertung) {
			var bewertungen = this.bewertung.getBewertungen();

			for (var i = 0; i < bewertungen.length; i++) {
				var bewertung = bewertungen[i];
				try {
					if (bewertung.prepareHTML) {
						bewertung.prepareHTML(this);
					}
				}
				catch (ex) {
					s += "\nBei der Anpassung " + bewertung.nr + " (" + bewertung.txt + ") trat ein Fehler auf:\n" + ex.message;
					console.info(ex);
				}
			}
		}
	}

	this.bewerte = function () {
		console.info(`bewerte ${this.layerId} Datensatz=${this.datensatzNr}`, this.habitate, this.nebencodes, this.gefcodes);
		if (arguments && arguments[0] && arguments[0].target && arguments[0].target.id && arguments[0].target.id === '144_see_gr_50ha_0') { return; }
		var s = "", value;

		if (!this.habitate) {
			this.readHabitate();
			this.readGefaehrdungen();
			this.readNebencodes();
			console.info("bewerte nach read" + this.layerId + " Datensatz=" + this.datensatzNr, this.habitate, this.nebencodes, this.gefcodes);
		}

		if (this.bewertung) {
			var bewertungen = this.bewertung.getBewertungen();

			for (var i = 0; i < bewertungen.length; i++) {
				var bewertung = bewertungen[i];

				try {
					if (bewertung.berechne) {
						bewertung.berechne(this);
					}
					if (bewertung.bewerte) {
						bewertung.bewerte(this);
					}
					if (bewertung.getKorrekturWert) {
						bewertung.berechneKorrekturWert(this);
					}
				}
				catch (ex) {
					s += "\nBei der Bewertung " + bewertung.nr + " (" + bewertung.txt + ") trat ein Fehler auf:\n" + ex.message;
					console.info(ex);
				}
			}

			for (var i = 0; i < bewertungen.length; i++) {
				var bewertung = bewertungen[i];
				console.info(bewertung.nr);
				try {
					if (bewertung.bewerte) {
						var nr = bewertung.nr;
						var txt = this.txt;
						var lrtCodes = this.lrtCodes;
						// console.info("bewertung["+i+"] "+nr+" result=");
						if (bewertung.cell) {
							value = bewertung.getResult();
							if (value === -1) {
								bewertung.cell.innerText = "f.A.";
								bewertung.cell.title = "Angaben fehlen";
								bewertung.cell.style.backgroundColor = "gray";
							} else if (value === 0) {
								bewertung.cell.innerText = "";
								bewertung.cell.title = "Die Eingangswerte für den Bewertungsalgorithmus ergeben keinen Zustand.";
								bewertung.cell.style.backgroundColor = "gray";
							} else {
								if (value.err) {
									bewertung.cell.title = value.err;
									bewertung.cell.innerHTML = '<img src="graphics/emblem-important.png" border="0">';
									bewertung.cell.style.backgroundColor = "gray";
								}
								else {
									if (value.skiped) {
										// bewertung.cell.innerText="";
										bewertung.cell.innerHTML = '<img src="graphics/emblem-important.png" border="0">';
										if (value.msg) {
											bewertung.cell.title = "Bewertung entfällt. " + value.msg;
										} else {
											bewertung.cell.title = "Bewertung entfällt";
										}
										bewertung.cell.style.backgroundColor = "#444";
									}
									else {
										bewertung.cell.innerText = value.b;
										bewertung.cell.style.backgroundColor = getColor(value.b);
									}
								}
							}
						}
						this.frame.setBewertung(bewertung);
					}
					if (bewertung.getKorrekturWert) {
						// console.info("DatensatzBewertung.bewerte ", bewertung.nr);
						if (bewertung.cell) {
							var value = bewertung.getKorrekturWert();
							if (Number.isInteger(value)) {
								bewertung.cell.innerText = value;
								// bewertung.cell.style.backgroundColor="lightblue";
								bewertung.cell.style.backgroundColor = getColor(bewertung.resultKorrigiert);
							}
							else {
								if (value && value.err) {
									bewertung.cell.title = value.err;
									bewertung.cell.innerHTML = '<img src="graphics/emblem-important.png" border="0">';
									bewertung.cell.style.backgroundColor = "gray";
								}
								else {
									bewertung.cell.innerHTML = '';
									bewertung.cell.style.backgroundColor = "gray";
								}
							}
						}
						this.frame.setBewertung(bewertung);
					}
				}
				catch (ex) {
					s += "\nBei der Bewertung " + bewertung.nr + " (" + bewertung.txt + ") trat ein Fehler auf:\n" + ex.message;
					console.info(ex);
				}
			}

			if (s.length > 1) {
				new showText(s);
			}
		}
		console.info(`bewerte ${this.layerId} Datensatz=${this.datensatzNr} done`);
	}

	this.addElement = function (inputElement) {
		inputElement.addEventListener('change', this.bewerte.bind(this));
		if (inputElement.type === 'text' || inputElement.type === 'textarea') {
			inputElement.addEventListener('keyup', this.bewerte.bind(this));
		}
		var id = inputElement.id;


		var idx = id.indexOf("_");
		var variableName = id.substring(idx + 1);
		idx = variableName.lastIndexOf("_");
		variableName = variableName.substring(0, idx);
		if (inputElement.type != "text" && inputElement.type != "textarea" && inputElement.type != "hidden" && inputElement.tagName.toLowerCase() != "select") {
			idx = variableName.lastIndexOf("_");
			if (idx > 0) {
				variableName = variableName.substring(0, idx);
			}
		}
		console.info(inputElement.id + "  " + inputElement.type + "  " + inputElement.tagName + "  " + variableName);
		if (variableName !== 'filter') {
			if (!this.map[variableName]) {
				this.map[variableName] = [];
			}
			try {
				this.map[variableName].push(inputElement);
			}
			catch (e) {
				console.info(inputElement.id, e);
			}
		}
	}

	this.getBoolValue = function (valueName) {
		var k = valueName.toLowerCase();
		var sValue, value, id, idA, ele;
		var elements = this.map[k];
		var result;
		var multiChoices = false;
		if (!elements || elements.length !== 2) {
			throw new Error("Elements kein Boolean bei getValue(\"" + valueName + "\") " + elements);
		}
		else {
			if (elements[0].checked) {
				return elements[0].value === true || elements[0].value === "t";
			}
			if (elements[1].checked) {
				return elements[1].value === true || elements[1].value === "t";
			}
		}
		return -1;
	}

	/**
	 * 
	 */
	this.disable = function (valueName, disabled) {
		var i, k = valueName.toLowerCase();
		var elements = this.map[k];
		if (elements && elements.length) {
			for (i = 0; i < elements.length; i++) {
				elements[i].disabled = disabled;
				if (disabled && elements[i].checked) {
					elements[i].checked = false;
				}
			}
		}
	}


	this.getSectionTitleElement = function (valueName) {
		let k = valueName.toLowerCase();
		let elements = this.map[k];
		let result;
		if (!elements || elements.length === 0) {
			console.info("elements null oder leer bei getValue(\"" + valueName + "\") " + elements);
		}
		if (elements && elements.length > 0) {
			result = elements[0];
		}

		while (result.parentNode && result.tagName !== "TABLE") {
			result = result.parentNode;
			console.info(result.tagName);
		}
		if (result.parentNode) {
			result = result.parentNode;
			if (result.tagName === "DIV") {
				result = result.querySelector("span.fett");
			}
		}
		console.info("getSection ", result);
		return result;
	}

	this.getTextValue = function (valueName) {
		var k = valueName.toLowerCase();
		var sValue, value, id, idA, ele;
		var elements = this.map[k];
		if (!elements || elements.length === 0) {
			console.info("elements null oder leer bei getValue(\"" + valueName + "\") " + elements);
		}
		if (elements && elements.length === 1) {
			return elements[0].value;
		}
	}

	this.setTextValue = function (valueName, value) {
		var k = valueName.toLowerCase();
		var sValue, value, id, idA, ele;
		var elements = this.map[k];
		if (!elements || elements.length === 0) {
			console.info("elements null oder leer bei getValue(\"" + valueName + "\") " + elements);
		}
		if (elements && elements.length === 1) {
			elements[0].value = value;
		}
	}

	this.getValue = function (valueName) {
		var k = valueName.toLowerCase();
		var sValue, value, id, idA, ele;
		var elements = this.map[k];
		var result = [];
		var multiChoices = false;
		if (!elements || elements.length === 0) {
			console.info("elements null oder leer bei getValue(\"" + valueName + "\") " + elements);
		}
		if (elements) {
			if (elements.length === 1) {
				if (elements[0].type === "checkbox") {
					return elements[0].checked;
				} else if (elements[0].type === "radio") {
					return elements[0].checked;
				}
				return elements[0].value;
			}
			else {
				for (var i = 0; i < elements.length; i++) {
					multiChoices = elements[i].type === "checkbox";
					if (elements[i].checked) {
						ele = elements[i];
						if (ele.type === "radio") {
							sValue = ele.value;
						}
						if (ele.type === "checkbox") {
							id = elements[i].id;
							idA = id.split("_");
							sValue = idA[idA.length - 2];
						}
						value = parseInt(sValue);
						result.push(value);
					}
				}
			}
		}
		if (multiChoices) {
			return result;
		}
		return result.length === 1 ? result[0] : -1;
	}

	this.getElements = function (valueName) {
		var k = valueName.toLowerCase();
		return this.map[k];
	}

	this.setRadioButton = function (valueName, value) {
		var k = valueName.toLowerCase();
		var sValue, value, id, idA, ele;
		var elements = this.map[k];
		var result = [];
		var multiChoices = false;
		if (!elements || elements.length === 0) {
			console.info("elements null oder leer bei getValue(\"" + valueName + "\") " + elements);
		}
		if (elements) {
			for (var i = 0; i < elements.length; i++) {
				ele = elements[i];
				sValue = ele.value;
				ele.checked = (sValue == value);
			}
		}
		if (multiChoices) {
			return result;
		}
		return result.length === 1 ? result[0] : -1;
	}

	this.setToolTip = function (valueName, txt) {
		var k = valueName.toLowerCase();
		var elements = this.map[k];
		if (elements && elements.length > 0) {
			elements[0].parentNode.title = txt;
		}
	}

	this.setCalculated = function (valueName, txt) {
		var k = valueName.toLowerCase();
		var elements = this.map[k];
		if (elements && elements.length > 0) {
			for (let i = 0; i < elements.length; i++) {
				elements[i].disabled = true;
				if (i === 0 && txt) {
					elements[i].parentNode.title = txt;
					elements[i].parentNode.style.backgroundColor = colorDisabled;
				}
			}
		}
	}

	this.getNebenCodes = function () {

		let results = [];
		try {
			let s = this.layerId + "_nc_" + this.datensatzNr;
			let e = document.getElementById(s);
			let elements = e.getElementsByClassName("subFormListItem");
			let count = elements.length;
			let f;
			for (let i = 0; i < count; i++) {
				f = elements[i];
				let a = f.getElementsByTagName("a");
				let sA = a[0].innerText.split(" ");
				let code = sA[0]
				let name = "";
				for (let aNr = 0; aNr < sA.length - 2; aNr++) {
					name += sA[aNr];
				}
				let percentage = Number(sA[sA.length - 2]);
				results.push(new NebenCodeRecord(code, name, percentage));
			}
		} catch (e) {
			console.error(e);
		}
		return results;
	}

	this.getHauptCode = function () {
		try {
			let sHCP = this.getValue("hcp");
			let elem = document.getElementById(this.layerId + "_hc_" + datensatzNr);
			if (elem) {
				console.info(elem, elem.type);
				let sHC = null;
				//if (elem.type && elem.type === 'text') {
				if (elem.type && elem.type === 'hidden') {		// Stefan 21.06.2024
					sHC = elem.value;
				} else {
					if (elem.selectedOptions) {
						sHC = elem.selectedOptions[0].innerText;
					} else {
						sHC = elem.options[elem.selectedIndex].text;
					}
				}
				let sA = sHC.split(" - ");
				if (sA.length > 0) {
					return new NebenCodeRecord(sA[0], sA[1], Number(sHCP));
				}
			}
		}
		catch (ex) {
			console.error(ex);
		}
	}

	this.getAnzahlNebenCodes = function () {
		var s = this.layerId + "_nc_" + this.datensatzNr;
		var e = document.getElementById(s);
		var elements = e.getElementsByClassName("subFormListItem");
		var i, count = elements.length;
		var f;
		for (i = 0; i < count; i++) {
			f = elements[i];
			var a = f.getElementsByTagName("a");
			console.info(a.innerText);
		}
		elements = e.getElementsByTagName("a");
		var i, count = elements.length;
		return count;

	}

	this._getLRTCode = function () {
		var s = this.layerId + "_lrt_code_" + this.datensatzNr;
		var el = document.getElementById(s);
		if (el) {

			var value = document.getElementById(s).value;
			console.log('_getLRTCode', typeof value, value);
			var iValue = parseInt(value);
			// ursprünglich waren die Werte nur Integer jetzt auch Text, desh. integer oder string
			return (value == ("" + iValue)) ? iValue : value;
		}
	}

	this.addListener50ha = function () {
		var el_gr_50ha = document.getElementById(this.layerId + "_see_gr_50ha_0");
		el_gr_50ha.addEventListener("change", () => {
			console.info("50ha changed " + el_gr_50ha.checked, el_gr_50ha);
		});
	}

	this.seeGr50ha = function () {
		var el_gr_50ha = document.getElementById(this.layerId + "_see_gr_50ha_0"),
			ret = null;

		if (el_gr_50ha) {
			if (el_gr_50ha.checked) {
				ret = true;
			}
			else {
				ret = false;
			}
		}
		else {
			throw new Error("Das Seegrösse Optionsfeld > 50ha fehlt.");
		}

		console.log('seeGr50ha: ', ret);
		return ret;
	}

	this.getFlaecheInHa = function (layerId, datensatzNr) {
		var el = document.getElementById(this.layerId + "_flaeche_" + this.datensatzNr);
		if (el && el.value) {

			const v = el.value.replace(",", ".");
			console.log('flaeche: %s', el.value, v, parseFloat(v));
			return parseFloat(v);
		}
		else {
			throw new Error("Kein Wert für die Fläche.");
		}
	}

	this.isBach = function () {
		return ["FBN", "FBB", "FBA"].indexOf(this.getValue("hc")) >= 0;
	}

	this.isFluss = function () {
		return ["FFN", "FFB", "FFA"].indexOf(this.getValue("hc")) >= 0;
	}

	this.isWald = function () {
		return this.layerId === 431;
	}


	this.validate = function (evt) {
		if (this.bewertung) {
			console.info("validate", this);
			return this.bewertung.validate();
		} else {
			console.info("no validating " + this.layerId + " " + this.datensatzNr);
			return true;
		}

	}

	this.submited = function (evt) {
		if (evt.target.status === 200) {
			console.info("Data saved");
		} else {
			console.info("Data not saved?", evt);
		}

	}

	this.submit = function () {
		let submitted = false;
		try {
			// damit kvwmap weiß, dass sich der Datensatz geändert hat
			const isDataChanged = setDataChanged(this.layerId, this.datensatzNr);
			if (isDataChanged) {
				const formElement = document.querySelector("form");
				formElement.go.value = 'Sachdaten_speichern';
				const formData = new FormData(formElement);
				const request = new XMLHttpRequest();
				const self = this;
				request.addEventListener('loadend', self.submited);
				request.open("POST", "index.php");
				request.send(formData);
				submitted = true;
			}
		} catch (err) {
			console.error(err);
		}
		console.info('werte wurden submitted? ' + submitted);
	}

}

function BewertungApp() {

	this.map = {};

	this.parse = function (tagName) {
		var inputElements = document.querySelectorAll(tagName);
		for (var i = 0; i < inputElements.length; i++) {
			var id = inputElements[i].id;
			var idA = id.split("_");
			var datensatzNr = Number.parseInt(idA[idA.length - 1], 10);
			var layerId = Number.parseInt(idA[0], 10);

			if (!isNaN(datensatzNr) && !isNaN(layerId) && datensatzNr >= 0 && datensatzNr != 109) {
				if (!this.map[layerId + datensatzNr]) {
					this.map[layerId + datensatzNr] = new DatensatzBewertung(layerId, datensatzNr);
				}
				this.map[layerId + datensatzNr].addElement(inputElements[i]);
			}
		}
	}

	function rbMousedown(evt) {
		let elem = evt.target;
		elem.iChecked = elem.checked;
	}
	function rbClicked(evt) {
		var elem = evt.target;
		if (!elem.iChecked) {
			elem.iChecked = elem.checked;
		}
		else {
			elem.checked = false;
			let changeEvt = new Event('change');
			elem.dispatchEvent(changeEvt);
		}
	}

	function makeRadioButtonUncheckable(rb) {
		rb.addEventListener("click", rbClicked.bind(rb));
		rb.addEventListener("mousedown", rbMousedown.bind(rb));
	}

	this.beforeSubmit = function (evt) {
		console.info("!!!!!!!!!!beforeSubmit");
		let submit = true;
		for (k in this.map) {
			try {
				let dsSubmit = this.map[k].validate(evt);
				if (submit && !dsSubmit) {
					submit = dsSubmit
				}
			}
			catch (ex) {
				alert("Die Bewertung konnte nicht gestartet werden:\n" + ex);
				console.error(ex);
			}
		}
		if (submit) {
			save();
		}
	}

	this.start = function () {
		/*
		let i;
				let elements = document.getElementsByTagName("input");
				for (i=0; i<elements.length; i++) {
						let rb = elements[i];
						if (rb.type==="radio") {
								makeRadioButtonUncheckable(rb);
						}
				}
		*/

		this.parse("input");
		this.parse("select");
		this.parse("textarea");
		for (k in this.map) {
			try {
				this.map[k].start();
			}
			catch (ex) {
				alert("Die Bewertung konnte nicht gestartet werden:\n" + ex);
				console.error(ex);
			}
		}

		var button = document.getElementById('sachdatenanzeige_save_button');
		if (button) {
			button.onclick = "";
			button.addEventListener("click", this.beforeSubmit.bind(this));
		}
		/*
		document.getElementById('sachdatenanzeige_save_button').onclick="";
		document.getElementById('sachdatenanzeige_save_button').addEventListener("click", this.beforeSubmit.bind(this));
		*/
	}

	if (!Number.isNumber) {
		Number.isNumber = function (v) {
			return !isNaN(v);
		}
	}
	if (!Number.isInteger) {
		Number.isInteger = function (v) {
			return !isNaN(v);
		}
	}
	if (!Number.parseInt) {
		Number.parseInt = function (v) {
			return parseInt(v);
		}
	}

	if (!String.fromCodePoint) {
		String.fromCodePoint = function (v) {
			// String.fromCharCode(v);
			return '';
		}
	}
}

/**
 * setzt das Flag zur Kontrolle, ob das sich der Datensatz geändert hat
 * wird durch kvwmap ausgewertet
 * @param {*} layerId 
 * @param {*} datensatzNr 
 * @returns true, wenn das flag gesetzt werden konnte, sonst false
 */
function setDataChanged(layerId, datensatzNr) {
	const dsDiv = document.getElementById('datensatz_' + layerId + '_' + datensatzNr);
	const dsCb = document.getElementById(layerId + '_' + datensatzNr);
	if (dsCb && dsCb.type === 'checkbox') {
		const sA = dsCb.name.split(';');
		if (sA.length === 5) {
			const qs = '[name=changed_' + layerId + '_' + sA[3] + ']';
			const hiddenField = dsDiv.querySelector(qs);
			if (hiddenField) {
				hiddenField.value = 1;
				return true;
			}
		}
	}
	return false;
}

/*
function loaded() {
	// (new BewertungApp()).start();
}
*/

// document.addEventListener("DOMContentLoaded", loaded);
// window.addEventListener('load', loaded);
