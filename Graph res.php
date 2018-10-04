<!doctype html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<title>Система ProsiZ</title>
	<link rel="icon" type="image/png" href="/logo256normal.png"/>
	<link href="/reset.css" rel="stylesheet" type="text/css">
	<link href="/Main.css" rel="stylesheet" type="text/css">
	<script src="/src/sigma.core.js"></script>
	<script src="/src/conrad.js"></script>
	<script src="/src/utils/sigma.utils.js"></script>
	<script src="/src/utils/sigma.polyfills.js"></script>
	<script src="/src/sigma.settings.js"></script>
	<script src="/src/classes/sigma.classes.dispatcher.js"></script>
	<script src="/src/classes/sigma.classes.configurable.js"></script>
	<script src="/src/classes/sigma.classes.graph.js"></script>
	<script src="/src/classes/sigma.classes.camera.js"></script>
	<script src="/src/classes/sigma.classes.quad.js"></script>
	<script src="/src/classes/sigma.classes.edgequad.js"></script>
	<script src="/src/captors/sigma.captors.mouse.js"></script>
	<script src="/src/captors/sigma.captors.touch.js"></script>
	<script src="/src/renderers/sigma.renderers.canvas.js"></script>
	<script src="/src/renderers/sigma.renderers.webgl.js"></script>
	<script src="/src/renderers/sigma.renderers.svg.js"></script>
	<script src="/src/renderers/sigma.renderers.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.labels.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.labels.prosiz.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.hovers.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.hovers.prosiz.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.nodes.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.nodes.prosiz.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.edges.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.edges.prosiz.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.edgehovers.def.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.edgehovers.prosiz.js"></script>
	<script src="/src/renderers/canvas/sigma.canvas.extremities.def.js"></script>
	<script src="/src/middlewares/sigma.middlewares.rescale.js"></script>
	<script src="/src/middlewares/sigma.middlewares.copy.js"></script>
	<script src="/src/misc/sigma.misc.animation.js"></script>
	<script src="/src/misc/sigma.misc.bindEvents.js"></script>
	<script src="/src/misc/sigma.misc.bindDOMEvents.js"></script>
	<script src="/src/misc/sigma.misc.drawHovers.js"></script>
	<script src="/plugins/sigma.parsers.json/sigma.parsers.json.js"></script>
	<script src="/plugins/sigma.plugins.dragNodes/sigma.plugins.dragNodes.js"></script>
	<script src="/js/jquery-3.3.1.js"></script>
	<script src="/header.js"></script>
</head>

<body id="container">
	<div id="contextmenu" class="task"></div>
	<style>
		#graph-container {
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			position: absolute;
		}
		
		#footer,
		#header,
		#contextmenu {}
	</style>
	<header id="header">
		<div class="header">
			<div id="LeftMenu" class="button">Вершина</div>
			<div class="Middle">
				<div class="logo"><?php include( ROOT . '/Greetengs.svg' );?></div>
			</div>
			<div id="RightMenu" class="button">Меню</div>
		</div>
		<div class="gradient-line"></div>
		<div id="LM" class="Menu">
			<div class="list LM_Block">
				<div id="NodeBlock" class="list Menu_Sub_Block" style="display: none">
					<div class="list Menu_Sub_Block">
						<div class="list_item Menu_Sub_Element">
							<div class="label">
								<label>Название: </label>
							</div>
							<div id="ChangeLabelInput" class="input">
								<input name="label" type="text" autocomplete="off"/>
							</div>
							<div id="ApplyChangeLabel" class="apply" title="Применить"></div>
							<div id="DeleteVertex" class="delete" title="Применить"></div>
						</div>
						<div class="list_item Menu_Sub_Element">
							<div class="label">
								<label>Статус: </label>
							</div>
							<div id="StatusButton" class="status noselect" title="Изменить статус">Изменить статус</div>
						</div>
						<div class="list_item Menu_Sub_Element" style="display: none">
							<div class="label">
								<label>Размер: </label>
							</div>
							<div id="ChangeSizeInput" class="input">
								<input name="size" type="number" autocomplete="off"/>
							</div>
							<div class="apply" title="Применить"></div>
						</div>
					</div>
					<div id="LinkListFrom" class="list Menu_Sub_Block">
						<div class="tlabel">
							<label>Связи из вершин</label>
						</div>
						<div class="list">
							<div class="list_item">
								<div class="id"></div>
								<div class="input">
									<input class="VertexName" autocomplete="off" value="">
								</div>
								<div class="create" title="Применить"></div>
								<div class="collapse" title="Стянуть" style="display: none"></div>
								<div class="bisect" title="Расщепить" style="display: none"></div>
								<div class="delete" title="Удалить" style="display: none"></div>
							</div>
						</div>
					</div>
					<div id="LinkListIn" class="list Menu_Sub_Block">
						<div class="tlabel">
							<label>Связи к вершинам</label>
						</div>
						<div class="list">
							<div class="list_item">
								<div class="id"></div>
								<div class="input">
									<input class="VertexName" autocomplete="off" value="">
								</div>
								<div class="create" title="Применить"></div>
								<div class="collapse" title="Стянуть" style="display: none"></div>
								<div class="bisect" title="Расщепить" style="display: none"></div>
								<div class="delete" title="Удалить" style="display: none"></div>
							</div>
						</div>
					</div>
					<div id="ApplyNodeChanges" class="Menu_Button">
						<label>Применить</label>
					</div>
					<div id="FindWay" class="Menu_Button">
						<label>Найти зависимости</label>
					</div>
				</div>
				<div id="EdgeBlock" class="Menu_Sub_Block" style="display: none">
					<div class="Menu_Sub_Block">
						<div id="EdgeFrom" class="label">
							<label>Из: </label><label></label>
						</div>
					</div>
					<div class="Menu_Sub_Block">
						<div id="EdgeIn" class="label">
							<label>В: </label><label></label>
						</div>
					</div>
					<div class="Menu_Sub_Block">
						<div class="label">
							<label>Размер: </label>
						</div>
						<div id="EdgeType" class="input">
							<input name="type" type="number" autocomplete="off"/>
						</div>
					</div>
					<div id="DeleteEdge" class="Menu_Button">
						<label>Удалить</label>
					</div>
					<div id="SplitEdge" class="Menu_Button">
						<label>Разбить ребро</label>
					</div>
					<div id="SplitEdge" class="Menu_Button">
						<label>Стянуть ребро</label>
					</div>
				</div>
			</div>
		</div>
		<div id="RM" class="Menu">
			<div id="AuthorizeButton" class="Menu_Button">
				<label>
					<?php echo $UserLabel?>
				</label>
			</div>
			<div class="RM_Block">
				<div id="AuthorizeBlock" class="Menu_Sub_Block" style="display: none">
					<form class="cent" action="/authorize.php" method="post">
						<div class="Menu_Sub_Block">
							<div class="label">
								<label>Email: </label>
							</div>
							<div class="input">
								<input name="mail" type="text" placeholder="e-mail"/>
							</div>
						</div>
						<div class="Menu_Sub_Block">
							<div class="label">
								<label>Пароль: </label>
							</div>
							<div class="input">
								<input name="password" type="password" placeholder="Пароль"/>
							</div>
						</div>
						<div class="Menu_Sub_Block">
							<div class="input">
								<input name="login_submit" type="submit" value="Представиться"/>
							</div>
							<div class="input">
								<input name="register_submit" type="submit" value="Зарегистрироваться"/>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="RM_Block">
				<div id="FindBlock" class="Menu_Sub_Block">
					<div class="input">
						<input name="findNode" type="text" placeholder="Поиск"/>
					</div>
				</div>
			</div>
			<?php if ( $user->type == 255 ) require( ROOT . '/GraphAdmRM.php' );?>
		</div>
	</header>
	<div id="graph-container"></div>
	<main>
		<?php if ( $user->NumberOfVisits != 0 ){
	echo '<style>
	.background_greetings{
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(19,26,34,1);
	transition: opacity 1s cubic-bezier(0.5, 0, 1, 0.5) 0s;
	}
	</style>';
	echo '<div class="background_greetings" id="Background_Greetings" onmousedown="this.remove();">';
	include( ROOT . '/Greetengs.svg' );
	echo '<script>
	(function (undefined) {document.getElementById("Background_Greetings").style.opacity = 0;
	setTimeout(function(){ document.getElementById("Background_Greetings").remove();},2000);}).call(this)</script>';
	echo '</div>';
	}?>
		<div id="BottomMenu">
			<div id="BottomMenuSettings">

			</div>
			<div id="BottomMenuGuide">

			</div>
		</div>
		<script>
			var SIGMA,
				GRAF;
			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', 'adapters/get_data.php', true );
			xhr.send();
			xhr.onerror = function () {
				alert( xhr.status + ': ' + xhr.statusText )
			};
			xhr.onload = function () {
				GRAF = JSON.parse( xhr.responseText );
				SIGMA = new sigma( {
					graph: GRAF,
					renderer: {
						container: 'graph-container',
						type: 'canvas'
					},
					settings: {
						prosiz: GRAF.settings,
						defaultEdgeColor: GRAF.settings.defaultEdgeColor,
						edgeColor: 'default',
						defaultEdgeType: 'prosiz',
						defaultNodeType: 'prosiz',
						defaultNodeColor: GRAF.settings.defaultNodeColor,
						defaultLabelHoverColor: '#b8c5d4',
						defaultHoverLabelBGColor: '#4D545C',
						minArrowSize: 5,
						defaultLabelSize: 20,
						fontStyle: 'Raleway ExtraLight',
						hoverFont: 'Raleway',
						hoverFontStyle: 'semibold',
						labelColor: 'default',
						defaultLabelColor: '#b8c5d4',
						labelHoverShadowColor: '#FFFFFF',
						borderSize: 1,
						defaultNodeBorderColor: '#FFD700',
						nodeHoverColor: "default",
						defaultNodeHoverColor: '#F81D1D',
						enableEdgeHovering: true,
						edgeHoverColor: "edge",
						defaultEdgeHoverColor: '#FFFFFF',
						edgeHoverSizeRatio: 1.5,
						edgeHoverExtremities: 'true'
					}
				} );
				GRAF.check.forEach( function ( c ) {
					SIGMA.graph.nodes( c.nodeid ).checked = true;
				} );
				var edges = SIGMA.graph.edges();
				var nodes = SIGMA.graph.nodes();
				edges.forEach( function ( e ) {
					e.sourceObj = SIGMA.graph.nodes( e.source );
					e.targetObj = SIGMA.graph.nodes( e.target );
					e.targetObj.istarget = true;
				} );
				nodes.forEach( function ( e ) {
					if ( !e.istarget ) e.root = true;
				} );
				edges.forEach( function ( e ) {
					if ( e.sourceObj.checked ) {
						e.targetObj.potential = true;
						e.topotential = true;
					}
				} );
				document.s = SIGMA;
				IniteHeader( SIGMA );
				// Initialize the dragNodes plugin:
				sigma.plugins.dragNodes( SIGMA, SIGMA.renderers[ 0 ] );
				SIGMA.refresh();
			}
		</script>
	</main>
	<footer id="footer"></footer>
</body>

</html>