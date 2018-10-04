function IniteHeader(s) {
	"use strict";
	$("#LeftMenu").click(ShowHideLeftMemu); // Показать / Скрыть правое меню
	$("#RightMenu").click(ShowHideRightMemu); // Показать / Скрыть левое меню
	$("#AuthorizeButton").click(AuthorizeButton); // Кнопка авторизоваться
	$("#AddVertex").click(AddVertex); // Кнопка добавить вершину
	$("#SaveAll").click(SaveAll); // Кнопка сохранить все в правом меню
	$("#select_vertex").click(selectname); // Выбор названия из списка
	$("#FindBlock .input input")[0].addEventListener('input', vertexnamechange); //
	$("#ChangeLabelInput")[0].addEventListener('input', vertexnamechange); //
	$("#ApplyNodeChanges").click(ApplyNodeChanges); // Кнопка применить
	$("#AddLinkFrom").click(AddLinkFrom); // Добавить связь из
	$("#AddLinkIn").click(AddLinkIn); // добавить связь В
	$("#FindWay").click(FindWay); // обработка нажатия кнопки поиска пути
	$("#DeleteVertex").click(DeleteVertex); //


	$("#LinkListFrom .create").bind("click", AddLinkFrom); //
	$("#LinkListIn .create").bind("click", AddLinkIn); //


	$("#BottomMenuSettings").bind("click", ShowSettings); //
	$("#BottomMenuGuide").bind("click", ShowGuide); //
	function ShowSettings(event) {
		if (event.target.parentElement.clientHeight < 500) {
			event.target.parentElement.style.height = "500px";
			event.target.parentElement.style.width = "500px";
		} else {
			event.target.parentElement.style.height = "110px";
			event.target.parentElement.style.width = "60px";
		}
	}

	function ShowGuide(event) {
		if (event.target.parentElement.clientHeight < 500) {
			event.target.parentElement.style.height = "500px";
			event.target.parentElement.style.width = "500px";
		} else {
			event.target.parentElement.style.height = "110px";
			event.target.parentElement.style.width = "60px";
		}
	}

	$(document).click(close);

	s.bind('clickStage', ClickStage);
	s.bind('click', close); // обработка щелчка мимо вершины графа
	s.bind('clickNode', ClickNode); // обработка щелчка по вершине графа
	s.bind('rightClick', ShowContextMenu);

	function ClickStage() {
		$("#NodeBlock").fadeOut();
		$("#EdgeBlock").fadeOut();
		if (document.s.selectedNode) document.s.selectedNode.selected = undefined;
		document.s.selectedNode = undefined;
	}

	if (supports_html5_storage()) {
		if (localStorage['NumberOfVisits']) {
			localStorage['NumberOfVisits']++
		} else {
			localStorage['NumberOfVisits'] = 1
		}
	}

	function supports_html5_storage() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}
	}

	function FindWay() {

		function clear(nodes, edges) {
			nodes.forEach(function (n) {
				n.finded = false;
			});
		}

		function find(id, obj) {
			document.s.graph.nodes(id).finded = true;
			edges.forEach(function (e) {
				if (e.target == id) {
					obj.push(e.source);
				}
			});
		}
		var i = 1;
		var nodes = document.s.graph.nodes();
		var edges = document.s.graph.edges();
		var IDCOL = [];
		var _IDCOL = [];
		IDCOL[0] = document.s.selectedNode.id;
		clear(nodes, edges);
		while (i) {
			i = 0;
			_IDCOL = [];
			for (var id in IDCOL) {
				find(IDCOL[id], _IDCOL);
				i++;
			}
			IDCOL = _IDCOL;
		}
		document.s.refresh();
	}

	function ApplyNodeChanges() {
		document.s.refresh();
	}

	function ShowContextMenu(event) {
		console.log("Context Menu");
		var contextmenu = document.getElementById("contextmenu");
		contextmenu.style.display = 'inline';
		contextmenu.style.top = event.data.clientY.toString() + "px";
		contextmenu.style.left = event.data.clientX.toString() + "px";

		document.s.graph.addNode({
			id: 999,
			label: "tt99",
			size: 1,
			x: sigma.utils.getX(event) - sigma.utils.getCenter(event).x,
			y: sigma.utils.getY(event) - sigma.utils.getCenter(event).y
		});
		document.s.refresh();
	}

	function SetData(reqdata, func, params) {
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify(reqdata));
		XHR.onload = function () {
			if (XHR.readyState === XHR.DONE) {
				switch (XHR.status) {
					case 200:
						responseText = JSON.parse(XHR.responseText);
					case 204:
						func(params);
				}
			}
		};
	}

	function LoadUserSettings() {
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/get_settings.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send();
		XHR.onload = function () {};
	}

	function SaveUserSettings() {
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_settings.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify(document.settings));
		XHR.onerror = function () {};
	}

	$("#StatusButton").click(ChangeStatus);

	function ChangeStatus() {
		function CheckNode(node, grade) {
			if (node.potential || node.root) {
				var XHR = new XMLHttpRequest();
				XHR.open('POST', 'adapters/check_node.php', true);
				XHR.setRequestHeader('Content-Type', 'application/json');
				XHR.send(JSON.stringify({
					id: node.id,
					grade: grade
				}));
				XHR.onload = function () {
					node.checked = true;
					document.s.graph.edges().forEach(function (e) {
						if (e.sourceObj.checked) {
							e.targetObj.potential = true;
							//e.topotential = true;
						}
					});
					document.s.refresh();
				};
				return true;
			} else {
				return false;
			};
		}

		function unCheckNode(node) {
			if (node.checked) {
				var XHR = new XMLHttpRequest();
				XHR.open('POST', 'adapters/uncheck_node.php', true);
				XHR.setRequestHeader('Content-Type', 'application/json');
				XHR.send(JSON.stringify({
					id: node.id
				}));
				XHR.onload = function () {
					node.checked = undefined;
					document.s.graph.edges().forEach(function (e) {
						if (!e.sourceObj.checked) {
							e.targetObj.potential = undefined;
							//e.topotential = undefined;
						}
					});
					document.s.refresh();
				};
				return true;
			} else {
				return false;
			}
		}
		if (this.innerText === 'Пройдено') {
			unCheckNode(document.s.selectedNode);
			this.innerText = 'Не прйдено';
		} else if (this.innerText === 'Не пройдено') {
			if (CheckNode(document.s.selectedNode, 5)) {
				this.innerText = 'Пройдено';
			} else {
				console.warn('Вы не можете изучить эту тему, сначала изучите предыдущие.');
			}
		} else {
			console.warn('Что-то случилось со страницей, обновите её пожалуйста');
		}
		document.s.refresh();
	}

	function DeleteVertex() {
		var ID = document.s.selectedNode.id;
		if (ID === undefined) {
			return;
		}
		SetData({
				deletevertex: [{
					id: ID
				}]
			},
			function (obj) {
				s.graph.dropNode(obj.id);
				s.refresh();
				$("#NodeBlock").fadeOut();
			}, {
				id: ID
			});
	}

	function delstr() {
		var ID = Number(this.parentElement.getElementsByClassName("id")[0].innerText);
		var requestData = {
			deletelink: [{
				id: ID
			}]
		};
		SetData(requestData,
			function (obj) {
				s.graph.dropEdge(obj.id);
				s.refresh();
				obj.th.parentElement.remove();
			}, {
				id: ID,
				t: this
			});
	}

	function apply(event) {
		var In = this.parentElement.parentElement.id == "LinkListIn";
		if (this.className == "apply") { // Если добавляем новую связь
			requestData.addlink = [{
				source: {
					label: (!In ? this.parentElement.getElementsByClassName("VertexName")[0].value : s.selectedNode.label)
				},
				target: {
					label: (In ? this.parentElement.getElementsByClassName("VertexName")[0].value : s.selectedNode.label)
				}
			}];
		} else if (this.className == "refresh") {}
		SetData(requestData,
			function (obj) {
				var list = JSON.parse(obj.xhr.responseText);
				s.graph.addEdge({
					id: list.id,
					target: list.target,
					source: list.source,
					type: "arrow"
				});
				obj.t.className = "refresh";
				s.refresh();
			}, {
				xhr: XHR,
				t: this
			});
	}

	function ClickNode(event) {
		if (s.selectedNode) s.selectedNode.selected = undefined;
		s.selectedNode = event.data.node;
		event.data.node.selected = true;
		s.refresh();
		var node = s.selectedNode;
		var id = node.id;
		var edges = document.s.graph.edges();

		$("#LinkListIn .list .list_item:last .input input").bind("input", vertexnamechange);
		$("#LinkListFrom .list .list_item:last .input input").bind("input", vertexnamechange);
		var lli = $("#LinkListIn .list .list_item:last").clone(true);
		var llI = $("#LinkListIn .list .list_item:last").clone(false);
		var llf = $("#LinkListFrom .list .list_item:last").clone(true);
		var llF = $("#LinkListFrom .list .list_item:last").clone(false);

		$("#NodeBlock #ChangeLabelInput input")[0].value = event.data.node.label;
		$("#NodeBlock #ChangeSizeInput input")[0].value = event.data.node.size;

		if (event.data.node.checked) {
			$("#StatusButton")[0].innerText = 'Пройдено';
		} else {
			$("#StatusButton")[0].innerText = 'Не пройдено';
		}

		$("#LinkListFrom .list").empty();
		$("#LinkListIn .list").empty();
		for (var i = 0; i < edges.length; i++) {
			if (edges[i].target == id) {
				var llFr = $("#LinkListFrom .list");
				llFr = llFr.append(llF.clone(false)).find(".list_item:last");
				llFr.find(".create").addClass("apply").removeClass("create");
				llFr.find(".collapse").fadeIn()[0].addEventListener('click', collapse);
				llFr.find(".bisect").fadeIn()[0].addEventListener('click', bisect);
				llFr.find(".delete").fadeIn()[0].addEventListener('click', delet);
				llFr.find(".input input")[0].oninput = vertexnamechange;
				llFr.find(".input input")[0].value = document.s.graph.nodes(edges[i].source).label;
				llFr.find(".id")[0].innerText = edges[i].id;
			}
			if (edges[i].source == id) {

				var llIn = $("#LinkListIn .list");
				llIn = llIn.append(llI.clone(false)).find(".list_item:last");
				llIn.find(".create").addClass("apply").removeClass("create");
				llIn.find(".collapse").fadeIn()[0].addEventListener('click', collapse);
				llIn.find(".bisect").fadeIn()[0].addEventListener('click', bisect);
				llIn.find(".delete").fadeIn()[0].addEventListener('click', delet);
				llIn.find(".input input")[0].addEventListener('input', vertexnamechange);
				llIn.find(".input input")[0].value = document.s.graph.nodes(edges[i].target).label;
				llIn.find(".id")[0].innerText = edges[i].id;
			}
		}
		var llFr = $("#LinkListFrom .list");
		llFr.append(llf.clone(true));
		var llIn = $("#LinkListIn .list");
		llIn.append(lli.clone(true));
		$("#NodeBlock").fadeIn();
	}

	function collapse() {

	}

	function bisect() {

	}

	function ShowHideRightMemu() {
		var p = $("#RM");
		if (p.css("display") === "none") {
			p.show("slow");
		} else {
			p.hide("slow");
		}
	}

	function ShowHideLeftMemu() {
		var p = $("#LM");
		if (p.css("display") === "none") {
			p.show("slow");
		} else {
			p.hide("slow");
		}
	}

	function AuthorizeButton() {
		var p = $("#AuthorizeBlock");
		if (p.css("display") === "none") {
			p.css("display", "block");
		} else {
			p.css("display", "none");
		}
	}

	function AddVertex() {
		var vert = '<div class="list_item"><div class="lli"><div class="label"><label>Название:</label></div><div class="input"><input class="VertexName" autocomplete="off"></div><div style="display: none"></div></div><div class="del" title="Удалить"></div><div class="apply" title="Сохранить"></div></div>';
		$("#VertexList").append(vert);
		$("#VertexList .list_item:last .del")[0].addEventListener('click', delstr);
		$("#VertexList .list_item:last .apply")[0].addEventListener('click', addvertex);
		$("#VertexList .list_item:last .input")[0].addEventListener('input', vertexnamechange);
	}

	function SaveAll() { //Проверяем каждую вершину, если она помечена флагом, то записываем
		var requestData = {
			changevertex: [],
			addvertex: [],
			addlink: []
		};
		var nodes = document.s.graph.nodes();
		nodes.forEach(function (item, i, arr) {
			if (item.changed === true) {
				requestData.changevertex.push({
					id: item.id,
					label: item.label,
					x: item.x,
					y: item.y,
					size: item.size
				});
			}
		});
		var VertexList = document.getElementById('VertexList');
		for (var i = 0; i < VertexList.children.length; i++) {
			requestData.addvertex.push({
				label: VertexList.children[i].children[0].children[1].children[0].value,
				x: 0,
				y: 0,
				size: 1
			});
		}
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		//XHR.onreadystatechange = ...;
		XHR.send(JSON.stringify(requestData));
	}

	function vertexnamechange() {
		var t = this;
		window.selected = t;
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/get_names.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		var arr = this.value.split(" ");
		XHR.send(JSON.stringify(arr));
		XHR.onload = function () {
			var mlist = JSON.parse(XHR.responseText);
			var list = "";
			var len = mlist.length;
			for (var i = 0; i < len && i < 11; i++) {
				list += "<li>" + "<label>" + mlist[i].label + "</label>" + '<label style="display: none">' + mlist[i].id + "</label>" + "</li>";
			}
			$("#select_vertex").css("width", t.offsetWidth).css("display", "block").offset({
				top: t.offsetTop + t.offsetHeight,
				left: t.offsetLeft
			});
			$("#select_vertex ul").html(list);
		};
	}

	function selectname(event) {
		window.selected.value = event.target.innerText;
		$("#select_vertex .list").empty();
	}

	function close(event) {
		if ($(event.target).closest("#select_vertex").length) {
			return;
		}
		$("#select_vertex").css("display", "none");
	}

	function addvertex(event) {
		var requestData = {
			change: [],
			addvertex: [],
			addlink: []
		};
		requestData.addvertex.push({
			label: event.currentTarget.parentElement.firstChild.children[1].firstChild.value,
			x: 0,
			y: 0,
			size: 1
		});

		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify(requestData));

		XHR.onreadystatechange = function () {
			if (XHR.readyState === XHR.DONE) {
				var list = JSON.parse(XHR.responseText);
				list.addvertex.forEach(function (item, i, arr) {
					document.s.graph.addNode({
						id: item.Rdata.id,
						label: item.Rdata.label,
						size: 1,
						x: 1, //sigma.utils.getX(event) - sigma.utils.getCenter(event).x,
						y: 1 //sigma.utils.getY(event) - sigma.utils.getCenter(event).y
					});
				});
				document.s.refresh();
				event.target.parentElement.remove();
			}
		};
	}

	function delet() {
		var t = this;
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify({
			deletelink: [{
				id: this.parentElement.children[0].innerText
			}]
		}));
		XHR.onload = function () {
			t.parentNode.remove();
			var list = JSON.parse(XHR.responseText).deletelink[0].Rdata;
			s.graph.dropEdge(list.id);
			s.refresh();
		};
	}

	function AddLinkFrom() {
		var lli = $("#LinkListFrom .list .list_item:last");
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify({
			addlink: [{
				source: {
					label: lli.find("input")[0].value
				},
				target: {
					id: s.selectedNode.id
				}
			}]
		}));
		XHR.onload = function () {
			var list = JSON.parse(XHR.responseText).addlink[0].Rdata;
			s.graph.addEdge({
				id: list.id,
				target: list.target.id,
				source: list.source.id,
				targetObj: s.graph.nodes(list.target.id),
				sourceObj: s.graph.nodes(list.source.id)
			});
			lli.after(lli.clone(true));
			$("#LinkListFrom .list .list_item:last input").val("");
			lli.find(".create:last").addClass("apply").removeClass("create").unbind();
			lli.find(".collapse").bind("click", collapse).fadeIn();
			lli.find(".bisect").bind("click", bisect).fadeIn();
			lli.find(".delete").bind("click", delet).fadeIn();
			s.refresh();
		};
	}

	function AddLinkIn() {
		var lli = $("#LinkListIn .list .list_item:last");
		var XHR = new XMLHttpRequest();
		XHR.open('POST', 'adapters/set_data.php', true);
		XHR.setRequestHeader('Content-Type', 'application/json');
		XHR.send(JSON.stringify({
			addlink: [{
				source: {
					id: s.selectedNode.id
				},
				target: {
					label: lli.find("input")[0].value
				}
			}]
		}));
		XHR.onload = function () {
			var list = JSON.parse(XHR.responseText).addlink[0].Rdata;
			s.graph.addEdge({
				id: list.id,
				target: list.target.id,
				source: list.source.id,
				targetObj: s.graph.nodes(list.target.id),
				sourceObj: s.graph.nodes(list.source.id)
			});
			lli.after(lli.clone(true));
			$("#LinkListIn .list .list_item:last input").val("");
			lli.find(".create:last").addClass("apply").removeClass("create").unbind();
			lli.find(".collapse").bind("click", collapse).fadeIn();
			lli.find(".bisect").bind("click", bisect).fadeIn();
			lli.find(".delete").bind("click", delet).fadeIn();
			s.refresh();
		};
	}
}
