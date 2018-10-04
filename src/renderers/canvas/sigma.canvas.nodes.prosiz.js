;
(function () {
	'use strict';

	sigma.utils.pkg('sigma.canvas.nodes');

	/**
	 * The default node renderer. It renders the node as a simple disc.
	 *
	 * @param  {object}                   node     The node object.
	 * @param  {CanvasRenderingContext2D} context  The canvas context.
	 * @param  {configurable}             settings The settings function.
	 */
	sigma.canvas.nodes.prosiz = function (node, context, settings) {
		var prefix = settings('prefix') || '';
		
		if (node.selected) {
			context.beginPath();
			context.fillStyle = settings('prosiz').TargetBorderNodeColor;
			context.arc(
				node[prefix + 'x'],
				node[prefix + 'y'],
				node[prefix + 'size'] + 2,//settings('borderSize'),
				0,
				Math.PI * 2,
				true
			);
			context.closePath();
			context.fill();
		}
		
		context.fillStyle = node.color || settings('prosiz').DefaultNodeColor;
		if (node.potential) context.fillStyle = settings('prosiz').PotentialNodeColor;
		if (node.root) context.fillStyle = settings('prosiz').RootNodeColor;
		if (node.finded) context.fillStyle = settings('prosiz').FindedNodeColor;
		//if (node.selected) context.fillStyle = settings('prosiz').TargetNodeColor;
		if (node.checked) context.fillStyle = settings('prosiz').CheckedNodeColor;
		context.beginPath();
		context.arc(
			node[prefix + 'x'],
			node[prefix + 'y'],
			node[prefix + 'size'],
			0,
			Math.PI * 2,
			true
		);

		context.closePath();
		context.fill();
	};
})();
