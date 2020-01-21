<?php 

function dd($var, $die_flag = true){
	ob_start();
	var_dump($var);
	$dump = ob_get_clean();

	// style
	$style = '
		<style type="text/css">
			.dd-container{
				width: 100%;
				box-sizing: border-box;
				height: auto;
				padding: 20px 10px;
				background-color: #333;
				color: white;
			}

			.dd-container *{
		    font-family: Arial;
  			letter-spacing: .8px;
			}

			.dd-line{
				padding: 5px 20px;
			}

			.dd-margin{
				margin-left: 30px;
			}

			.dd-arrow{
				font-weight: bold;
				padding: 0 10px;
				color: #D9CB04;
			}

			.dd-key{
				font-weight: bold;
				padding: 0 3px;
				color: #038C8C;
			}

			.dd-key-border{
				color: #026873;
			}

			.dd-keyword{
				font-weight: bold;
				color: #D9B504;
				margin-right: 5px;
			}

			.dd-brackets-content{
				font-weight: bold;
				padding: 0 3px;
				color: #F28D77;
			}

			.dd-block{
				display: none;
			}

			.dd-block.show{
				display: block;
			}

			.dd-btn{
		    display: inline-block;
		    color: #7ED955;
		    background: transparent;
		    padding: 0;
		    width: 20px;
		    height: 20px;
		    cursor: pointer;
		    text-align: center;
		    outline: none;
		    font-size: 16px;
		    line-height: 18px;
		    border: 2px solid #7ED955;
			}

			.dd-block-show,
			.dd-block-hide{
        margin-top: -29px;
		    position: relative;
		    left: -10px;
		    float: right;
			}

			.dd-block-hide{
				line-height: 16px;
				color: #F28D77;
				border-color: #F28D77;
			}
		</style>
	';

	// JAVASCRIPT
	$js = '<script>
		let ddJS = function(){
			let btnsShow = document.getElementsByClassName("dd-block-show");
			for(let i in btnsShow){
				let btn = btnsShow[i];
				btn.onclick = function(){
					this.style.display = "none";
					let block = document.getElementsByClassName("dd-block-id-" + this.dataset.blockShow)[0];
					block.classList.add("show");
				}
			}

			let btnsHide = document.getElementsByClassName("dd-block-hide");
			for(let i in btnsHide){
				let btn = btnsHide[i];
				btn.onclick = function(){
					document.querySelector("[data-block-show=\"" + this.dataset.blockHide + "\"]").style.display = "inline-block";
					let block = document.getElementsByClassName("dd-block-id-" + this.dataset.blockHide)[0];
					block.classList.remove("show");
				}
			}
		}

		ddJS();
	</script>';

	$lines = explode("\n", $dump);
	$prev_lvl = 0;
	$cur_lvl = 1;
	foreach ($lines as $inx => $line) {
		$two_space = '<span class="dd-margin"></span>';
		$len = strlen($line);
		$count_spaces = 0;		

		for($i=0; $i<$len; $i++){
			if($line[$i] != " "){
				$count_spaces = $i;
				break;
			}
		}

		$line = mb_substr($line, $count_spaces, $len);
		for($i=0; $i<$count_spaces / 2; $i++){
			$line = $two_space . $line;
		}

		$lines[$inx] = '<div class="dd-line">' . $line . '</div>';

		$cur_lvl = $count_spaces / 2;
		if($prev_lvl < $cur_lvl){
			$bid = uniqid('', $inx);
			$lines[$inx] = '
			<button class="dd-btn dd-block-show" data-block-show="' . $bid . '">+</button>
			<div class="dd-block dd-block-lvl-
			' . $cur_lvl . ' dd-block-id-' . $bid . '">
			<button class="dd-btn dd-block-hide" data-block-hide="' . $bid . '">-</button>
			' . $lines[$inx];
		}

		if($prev_lvl > $cur_lvl){
			$lines[$inx] .= '</div>';
		}

		$prev_lvl = $cur_lvl;

	}

	$dump = implode('', $lines);

	$dump = str_replace('=>', '<span class="dd-arrow">>></span>', $dump);
	$dump = str_replace('["', '<span class="dd-key-border">["</span><span class="dd-key">', $dump);
	$dump = str_replace('"]', '</span><span class="dd-key-border">"]</span>', $dump);

	// keywords
	$keywords = ['array', 'string', 'int', 'float', 'double', 'object'];
	$formating_keywords = array_map(function($item){
		return '<span class="dd-keyword">' . $item . '</span>';
	}, $keywords);
	$dump = str_replace($keywords, $formating_keywords, $dump);

	// brackets content
	$dump = str_replace('(', '(<span class="dd-brackets-content">', $dump);
	$dump = str_replace(')', '</span>)', $dump);

	// Print data forming
	$dump = $style . '<div class="dd-container">' . $dump;
	$dump .= '</div>' . $js;

	echo $die_flag ? die($dump) : $dump;
}
