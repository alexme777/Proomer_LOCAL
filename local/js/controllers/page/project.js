(function($, APP) {
    'use strict';

    /**
     * Добавление дизайна
     **/
    APP.Controls.Page.Project = can.Control.extend({

		init: function () {
			var self = this;
			var b = null;
			APP.Controls.SelectMulti.initList(this.element.find('.js-select-city'));
			this.element.find('.js-select-city').select2({
                minimumResultsForSearch: 500,
                width: 'element',
               // dropdownParent: $('.select-city-container2')
            });
			new APP.Controls.PlanirovkaForm(this.element.find(".js-planirovka-form"));
			new APP.Controls.AddressForm(this.element.find(".js-address-form"));
			//new APP.Controls.Step5Form(this.element.find(".js-order-settings-form"));
			new APP.Controls.SupermanForm(this.element.find(".js-super-man-form"));
			new APP.Controls.FilterForm(this.element.find(".js-filter-form"));
			APP.Controls.AddProjectToBasket.initList(this.element.find(".js-add-basket"));
			new APP.Controls.DropzoneArea(this.element.find(".js-docs-dropzone"), {
				url: '/project/uploadplan',
				maxFilesize: 20,
				acceptedFiles: '.jpg, .png, .bmp',
				files: true,
				paramsQuery: {
					fileType: "DETAIL_PICTURE"
				}
			});
			this.element.find("form .js-type-1").select2({
				minimumResultsForSearch: 500,
				width: 'auto'
			});
			this.element.find("form .js-type-2").select2({
				minimumResultsForSearch: 500,
				width: 'auto'
			});
			this.element.find("form .js-type-3").select2({
				minimumResultsForSearch: 500,
				width: 'auto'
			});
			
		},
			
		'.js-show-hidden-filter click': function (el) {
			if(this.element.find(".btn_for_filter").hasClass('open')){
				this.element.find(".btn_for_filter").removeClass('open').addClass('close');
				this.element.find("#filter_plan .filter").hide();
			}
			else{
				this.element.find(".btn_for_filter").removeClass('close').addClass('open');
				this.element.find("#filter_plan .filter").show();
			}
		},
		
		'.js-list-plan click': function(el, e){
			var target = e.target;
			//console.log(e.target.className);
			while (target.className != 'row') {
				if (target.className == 'plan') {
					var optionsPlan = this.element.find("#designsPlan");
					optionsPlan.attr('value', '');
					var id = target.getAttribute('data-id');
					var optionsPlan = this.element.find("#optionsPlan");
					optionsPlan.attr('value', id);
					this.element.trigger("list.contentUpdate", {});
					
					var filterParams = $('.js-filter-form').serializeArray();

					var validFilterParams = $.map(filterParams, function (el) {
						return el.value.length > 0 ? el : null;
					});

					var getStr = $.map(validFilterParams, function (el) {
						return el.name + "=" + el.value;
					}).join("&");

					var newUrl = location.origin + location.pathname + "?" + getStr;
					history.pushState({}, '', newUrl);
					$.scrollTo($(".list-plan-options").offset().top - 100, 500);
					return;
				}
				else if(target.className == 'plan option'){
					var id = target.getAttribute('data-id');
					var optionsPlan = this.element.find("#designsPlan");
					optionsPlan.attr('value', id);
					this.element.trigger("list.contentUpdate", {});
					
					var filterParams = $('.js-filter-form').serializeArray();

					var validFilterParams = $.map(filterParams, function (el) {
						return el.value.length > 0 ? el : null;
					});

					var getStr = $.map(validFilterParams, function (el) {
						return el.name + "=" + el.value;
					}).join("&");

					var newUrl = location.origin + location.pathname + "?" + getStr;
					history.pushState({}, '', newUrl);
					$.scrollTo($(".list-plan-design").offset().top, 500);
					return;
				}
				target = target.parentNode;
			}
		},
		
		'.js-savestep1 click': function (el, e){
			e.preventDefault();
			var target = e.target;
			var $ajaxContent = this.element.find('.left_bn');
			var id_flat = this.element.find('#select_step_1').val();
			var id_plan = this.element.find('#select_step_2').val();
			var action = el.attr('data-next-step');
			var self = this;
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/project/savestep1/',
				data: 'id_flat=' + id_flat + '&id_plan=' + id_plan,
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					window.location.href = el.attr('href');
				})
			});
		},
		
		'.js-savestep2 click': function (el, e){
			e.preventDefault();
			var target = e.target;
			var $ajaxContent = this.element.find('.left_bn');
			var id_item = this.element.find('#select_step_2').val();
			var action = el.attr('data-next-step');
			var self = this;
			var data = [];
			for(var i = 0; i < this.element.find('.room_form').length; i++){
			//	data[i] = this.element.find('.js-room-form')[i].serializeArray();
				//var data = $('.room_form')[0].serializeArray();
				data = $('#room_n_'+i+' form.room_form').serializeArray();
						//здесь не очень хорошо все сделано, ибо получается дофига запросов
				
					$ajaxContent.ajaxl({
						topPreloader: false,
						url:'/project/savestep2/',
						data: data,
						dataType: 'HTML',
						type: 'POST',
						success: this.proxy(function (data) {
							var result = JSON.parse(data);
						})
					});
				
				
			}
			
	
			window.location.href = el.attr('href');
		},
		
		'.js-savestep3 click': function (el, e){
			e.preventDefault();
			var target = e.target;
			var $ajaxContent = this.element.find('.left_bn');
			var action = el.attr('data-next-step');
			var self = this;
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/project/savestep3/',
				data: 'id=652',
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					window.location.href = el.attr('href');
				})
			});
		},
		
		'.js-savestep4 click': function (el, e){
			e.preventDefault();
			var target = e.target;
			var $ajaxContent = this.element.find('.left_bn');
			var action = el.attr('data-next-step');
			var self = this;
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/project/savestep4',
				data: this.element.find('.js-family-form').serializeArray(),
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					window.location.href = el.attr('href');
				})
			});
		},
		
		'.js-savestep5 click': function (el, e){
			e.preventDefault();
			var target = e.target;
			var $ajaxContent = this.element.find('.left_bn');
			var action = el.attr('data-next-step');
			var self = this;
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/project/savestep5',
				data: this.element.find('.js-order-settings-form').serializeArray(),
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					
				})
			});
			
		},
				
		'.js-close-open-room click': function(el, e){
			if(el.hasClass('open')){
				el.removeClass('open');
				el.addClass('closeroom');
				$('#'+el.attr('data-for')).hide(300);
			}
			else{
				el.removeClass('closeroom');
				el.addClass('open');
				$('#'+el.attr('data-for')).show(300);
			}
		},
		
		'.js-select-room click': function(el, e){
			e.preventDefault();
			if(e.target.tagName == 'A' && e.target.classList.contains('room')){
				var target = e.target;
				if(e.target.classList.contains('selected')){e.target.classList.remove('selected');
					if($("#countRoom").attr('value')){
						var arr_count_room = JSON.parse($("#countRoom").attr('value'));
					}
					else{
						var arr_count_room = [];
					}
					var i = 0;
				
					for(i; i <= arr_count_room.length; i++){
						if(arr_count_room[i] == target.getAttribute('data-value')){
							arr_count_room.splice(i, 1);
							break;
						}
					};
				
					$("#countRoom").attr('value',JSON.stringify(arr_count_room));
					this.element.trigger("list.contentUpdate", {});
				}
				else{
					e.target.classList.add('selected');
					if($("#countRoom").attr('value')){
						var arr_count_room = JSON.parse($("#countRoom").attr('value'));
					}
					else{
						var arr_count_room = [];
					}
					var i = 0;
					var flag = 1;
					for(i; i <= arr_count_room.length; i++){
						if(arr_count_room[i] == target.getAttribute('data-value')){
							flag = 0;
							break;
						}
					};
					if(flag == 1){
						arr_count_room.push(+target.getAttribute('data-value'));
					}
					$("#countRoom").attr('value',JSON.stringify(arr_count_room));
					this.element.trigger("list.contentUpdate", {});
				}
				var filterParams = $('.js-filter-form').serializeArray();

                var validFilterParams = $.map(filterParams, function (el) {
                    return el.value.length > 0 ? el : null;
                });

                var getStr = $.map(validFilterParams, function (el) {
                    return el.name + "=" + el.value;
                }).join("&");

                var newUrl = location.origin + location.pathname + "?" + getStr;
                history.pushState({}, '', newUrl);
			}
		},
			
		'.js-finish-step click': function (el) {
			if (el.hasClass("disabled")) return;
			this.saveStep();
		},

		'.js-file-delete click': function (el) {
			this.element.ajaxl({
				url: APP.urls.designEdit.deleteFile,
				data: {designId: this.options.designIdInput.val(), fileType: "PROPERTY_DOCUMENTS"},
				dataType: 'JSON',
				type: 'POST',
				success: this.proxy(function (data) {
					el.closest(".upload-doc").remove();
					$(window).trigger("dropzone.checkItems");
				})
			});
		},
		
		'.js-open-page click': function (el, e){
			e.preventDefault();
			if(e.target.tagName == 'A'){
				var target = e.target;
				$.each(this.element.find(".row-btn-top > a.style2, .row-btn-top > a.style3"), function( i, item ) {
					item.classList.remove('style2');
					item.classList.add('style3');
				});
				target.classList.add('style2');
				target.classList.remove('style3');
				if(target.getAttribute('data-page') == 'select'){
					this.element.find(".project-upload").hide();
					this.element.find(".project-select").show();
				}
				else if(target.getAttribute('data-page') == 'upload'){
					this.element.find(".project-upload").show();
					this.element.find(".project-select").hide();
				}
			};
		},
		
		'.js-select-item-plan click': function (el, e){
			e.preventDefault();
			var target = e.target;
			
				$.each(this.element.find(".list-plan .plan"), function( i, item ) {
					item.classList.remove('selected');
				});
				
				while (!target.classList.contains("list-plan")) {
					if (target.classList.contains('plan')) {
						// нашли элемент, который нас интересует!
						target.classList.add('selected');
						var id_item = target.getAttribute('data-id-plan');
						var type = target.getAttribute('data-type');
						var $ajaxContent = this.element.find('.project-out');
						var $ajaxContent2 = this.element.find('.plan-list');
						if(type == 'apartment'){		
							$("#select_step_1").val(id_item);
						}
						else if(type == 'plan'){
							$("#select_step_2").val(id_item);
						};
						var self = this;
						$ajaxContent.ajaxl({
							topPreloader: false,
							url:'/project/projectcart',
							data: 'id_plan='+$("#select_step_1").val()+'&id_plan_option='+$("#select_step_2").val()+'&type='+type,
							dataType: 'HTML',
							type: 'POST',
							success: this.proxy(function (data) {
								$ajaxContent.html(data);
							})
						});
						
						$ajaxContent2.ajaxl({
							topPreloader: false,
							url:'/project/planlist',
							data: 'id_plan='+id_item+'&id_plan_option=',
							dataType: 'HTML',
							type: 'POST',
							success: this.proxy(function (data) {
								
								if(type == 'apartment'){
									$ajaxContent2.html(data);
								}

							})
						});
						
						return;
					}
					target = target.parentNode;
				}
		},
		
		'.js-submit-show click': function (el){
			//$('.list-plan .plan-item').removeClass('hide').addClass('show');
			//$('.js-submit-show').hide();
			var page = +$('#page').val();
			$('#page').val(page+1);
			var filterParams = $('.js-filter-form').serializeArray();

			var validFilterParams = $.map(filterParams, function (el) {
				return el.value.length > 0 ? el : null;
			});

			var getStr = $.map(validFilterParams, function (el) {
				return el.name + "=" + el.value;
			}).join("&");
			
			var newUrl = location.origin + location.pathname + "?page=" + page;
			history.pushState({}, '', newUrl);
			this.element.trigger("list.contentUpdate", {});
			return;
		},
		
		'.js-add-family click': function (el, e){
			var html = this.element.find('#people_family').html();
			var target = e.target;

			// цикл двигается вверх от target к родителям до table
			while (target.tagName != 'BODY') {
				if (target.tagName == 'FORM') {
				// нашли элемент, который нас интересует!
				$('#list-people-family', target).append(html);

				$(".js-type-1", target).select2({
				minimumResultsForSearch: 500,
				width: 'auto'
				});
				}
				target = target.parentNode;
			}

			
			//console.log(el.parent().children())	
		},
		
		'.js-add-family-children click': function (el, e){
			var html = this.element.find('#people_family_children').html();
			var target = e.target;

			// цикл двигается вверх от target к родителям до table
			while (target.tagName != 'BODY') {
				if (target.tagName == 'FORM') {
				// нашли элемент, который нас интересует!
				$('#list-people-children-family', target).append(html);

				$(".js-type-1", target).select2({
				minimumResultsForSearch: 500,
				width: 'auto'
				});
				}
				target = target.parentNode;
			}
			//console.log(el.parent().children())	
		},
		
		'.js-remove-family click': function (el, e){
			var target = e.target;

			// цикл двигается вверх от target к родителям до table
			while (target.tagName != 'BODY') {
				if (target.tagName == 'FORM') {
					$('#list-people-family .item-family:last', target).remove()
				}
				target = target.parentNode;
			}
		},
		
		'.js-remove-family-children click': function (el, e){
			//console.log(el.parent().children())
			var target = e.target;

			// цикл двигается вверх от target к родителям до table
			while (target.tagName != 'BODY') {
				if (target.tagName == 'FORM') {
					$('#list-people-children-family .item-family:last', target).remove()
				}
				target = target.parentNode;
			}
		},	
		'.js-select-design click': function (el, e){
			e.preventDefault();
			var target = e.target;
			while(target.className != "show-design"){
				
				if(target.className == 'picture_design'){
					console.log(target.getAttribute('src'));
					$('<input type="hidden" name="liked_design[]" value="'+target.getAttribute('src')+'"/>').appendTo(el);
				}
				if (target.className == 'design'){
					var val = +el.parent().parent().find('.progressBar input').val();
					el.parent().parent().find('.progressBar input').val(val + 1);
					$('.knob').trigger("change");
	
					var parent = $(target).parent().parent();
					parent.find('.selected').remove();
					//$(target.parent.children()).remove('.selected');
					$(target).append('<div class="selected"></div>');
					if(parent.next().hasClass('variant_designs')){
						parent.fadeOut();
						parent.next().fadeIn();
					};
					return;
				}
				target = target.parentNode;
			}
		},
		
		'#address-popup-input input': function(el, e) {
            var address = el.val();
			var $ajaxContent = this.element.find('.result');
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/search-service/step-plan-name/search',
				data: {'address': address},
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					$ajaxContent.html(data);
				})
			});
        },
		
		'.js-select-address click': function(el, e) {
			window.location.href = '/project/?street='+el.attr('data-street')+'&number='+el.attr('data-number');
		},
		
		'.js-search-form click': function(el, e){
			window.location.href = '/project/?address='+$('#address').val();;
		},
		
		'.js-search-form click': function(el, e){
			window.location.href = '/project/?address='+$('#address').val();;
		},
		
		'list.contentUpdate': function (el, e, param) {
			var $ajaxContent = this.element.find('.js-ajax-list-content');
			var data = [];

			if (param.page > 0) {
				data.push({
					name: "page",
					value: param.page
				})
			} else {
				data.push({
					name: "page",
					value: $("#page").val()
				})
			}

			if (param.viewCounter > 0) {
				data.push({
					name: "viewCounter",
					value: param.viewCounter
				})
			}

			this.element.find(".js-sort a").each(function(){
				data.push({
					name: "sort[" + $(this).data("type") + "]",
					value: $(this).data("method")
				})
			});

			$.merge(data, this.element.find('.js-filter-form').serializeArray());

			var self = this;
			$ajaxContent.ajaxl({
				topPreloader: true,
				url: location.pathname,
				data: data,
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					$ajaxContent.html(data);
					if ($ajaxContent.find('.not-found').length > 0) {
						//self.element.find('.filter').css({'visibility': 'hidden'});
					} else {
						//self.element.find('.filter').css({'visibility': 'visible'});
					}
					new APP.Controls.Pagination(this.element.find(".js-pagination"));
					$('.list-plan .plan-item').removeClass('hide').addClass('show');
					//$.scrollTo(this.element.find('.catalog-block'), 500);
					//new APP.Controls.Likes.initList(this.element.find('.js-like'));
					//APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
				})
			});
		},
			
		saveStep: function () {
			//Перевод проекта на модерацию
			var $stepBlock = this.element;

			$stepBlock.ajaxl({
				url: APP.urls.designEdit.publishDesign,
				data: {designId: this.options.designIdInput.val()},
				dataType: 'JSON',
				type: 'POST',
				success: this.proxy(function (data) {
					if (data.result) {
						APP.helpers.showFancyboxMessage("Отправлен на модерацию", "Ваш проект отправлен на модерацию. Администратор проверит его на соответствие нашим требованиям и опубликует на сайте. Или вернет на доработку — проверяйте email", 5000, "/profile/design/");
					}
				})
			});
		}
      
    });

})(jQuery, APP);