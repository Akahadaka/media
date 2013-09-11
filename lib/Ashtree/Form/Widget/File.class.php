<?php 
/**
 * 
 */
class Ashtree_Form_Widget_File extends Ashtree_Form_Widget {

	public $multiple;
	public $allowed_size = 0;
	public $allowed_type = array();

	public function __invoke($print=TRUE) {
		$dom = $this->dom;
		$upload_max_filesize = (int)ini_get("upload_max_filesize");
		$post_max_size       = (int)ini_get('post_max_size');
		$allowed_size        = ($this->allowed_size > 0 ) ? (int)$this->allowed_size : $post_max_size;

		// Field
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'field-wrapper ' . $this->class);

		// Label
		$lbl_wrp = $dom->createElement('div');
		$lbl_wrp->setAttribute('class', 'label-wrapper');

		$lbl = $dom->createElement('label');
		$lbl->setAttribute('for', $this->name);
		$lbl->setAttribute('class', 'label');
		$lbl->nodeValue = $this->label;
		
		// Required
		if ($this->required) {
			$lbl_req = $dom->createElement('span');
			$lbl_req->setAttribute('class', 'required');
			$lbl_req->nodeValue = ' *';
			$lbl->appendChild($lbl_req);
		}

		// Value
		$inp_wrp = $dom->createElement('div');
		$inp_wrp->setAttribute('class', 'value-wrapper');
		
		if ($this->value) {
			$out_wrp = $dom->createElement('ul');
			foreach((array)$this->value as $filename) {
				$out = $dom->createElement('a');
				$out->setAttribute('href', $filename);
				$out->setAttribute('target', '_blank');
				$out->nodeValue = pathinfo($filename, PATHINFO_BASENAME);
	
				$out_wrp->setAttribute('class', 'value-link-wrapper');
				$out_uli = $dom->createElement('li');
				$out_uli->appendChild($out);
				$out_wrp->appendChild($out_uli);
			}
			$inp_wrp->appendChild($out_wrp);
		}
		
		if (!$this->readonly) {
			$inp = $dom->createElement('input');
			$inp->setAttribute('type', 'file');
			if ($this->multiple || ($this->limit != 1)) {
				$inp->setAttribute('name', "{$this->name}[]");
				$inp->setAttribute('id', "{$this->name}_1");
			} else {
				$inp->setAttribute('name', $this->name);
				$inp->setAttribute('id', $this->name);
			}
			$inp->setAttribute('class', 'file value');
			$inp->setAttribute('title', $this->help);
			if ($this->multiple) $inp->setAttribute('multiple', 'multiple');
	
			// Output
			$lbl_wrp->appendChild($lbl);
			#$inp_wrp->appendChild($inp);
			$div->appendChild($lbl_wrp);
			if ($this->limit == 0) {
				$htm = Ashtree_Html_Page::instance();
				$htm->jquery = "
					$('.{$this->name}.file-minus').hide();
					$('.{$this->name}.file-plus').live('click', function(){
						var value_wrapper = $(this).parent();
						var value_wrapper_2 = value_wrapper.clone();
						value_wrapper.after(value_wrapper_2);
						$('input[type=file]', value_wrapper_2.parent()).each(function(){
							
							$(this).attr('id', '{$this->name}_' + ($(this).parent().index()+1));
							console.log($(this).attr('id'));
						});
						$(this).hide();
						$(this).next('.file-minus').show();
					});
					$('.{$this->name}.file-minus').live('click', function(){
						var value_wrapper = $(this).parent();
						value_wrapper.remove();
					});
				";
				$inp_num = $dom->createElement('ol');
				$inp_oli = $dom->createElement('li');
				$inp_two = $inp->cloneNode();
				
				$lim_pls = $dom->createElement('input');
				$lim_pls->setAttribute('type', 'button');
				$lim_pls->setAttribute('class', "{$this->name} file-plus");
				$lim_pls->setAttribute('value', 'Add');
				
				$lim_min = $dom->createElement('input');
				$lim_min->setAttribute('type', 'button');
				$lim_min->setAttribute('class', "{$this->name} file-minus");
				$lim_min->setAttribute('value', 'Remove');
				
				$inp_oli->appendChild($inp_two);
				$inp_oli->appendChild($lim_pls);
				$inp_oli->appendChild($lim_min);
				$inp_num->appendChild($inp_oli);
				$inp_wrp->appendChild($inp_num);
				
				$div->appendChild($inp_wrp);
			} else if ($this->limit > 1) {
				$inp_num = $dom->createElement('ol');
				for ($i = 1; $i <= $this->limit; $i++){
					$inp_oli = $dom->createElement('li');
					$inp_two = $inp->cloneNode();
					$inp_two->setAttribute('id', "{$this->name}_{$i}");
					$inp_oli->appendChild($inp_two);
					$inp_num->appendChild($inp_oli);
				}
				$inp_wrp->appendChild($inp_num);
				$div->appendChild($inp_wrp);
			} else {
				$inp_wrp->appendChild($inp);
				$div->appendChild($inp_wrp);
			}
			
			// Help
			$hlp_wrp = $dom->createElement('div');
			$hlp_wrp->setAttribute('class', 'help-wrapper');
			$max = $dom->createElement('div');
			$max->nodeValue = 'Max upload size: ' . min($upload_max_filesize, $post_max_size, $allowed_size) . 'MB';
			$hlp_wrp->appendChild($max);
			
			if (!empty($this->filetypes)) {
				$typ = $dom->createElement('div');
				$typ->nodeValue = 'File types limited to: .' . implode(', .', (array)$this->filetypes);
				$hlp_wrp->appendChild($typ);
			}
			$div->appendChild($hlp_wrp);
			
		} else {
			$lbl_wrp->appendChild($lbl);
			$div->appendChild($lbl_wrp);
			$div->appendChild($inp_wrp);
		}
		
		return $print ? $dom->saveXML($div) : $div;
	}
}