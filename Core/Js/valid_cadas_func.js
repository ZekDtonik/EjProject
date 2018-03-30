jQuery.validator.addMethod("cpf", function(value, element) {
    value = jQuery.trim(value);
 
     value = value.replace('.','');
     value = value.replace('.','');
     cpf = value.replace('-','');
     while(cpf.length < 11) cpf = "0"+ cpf;
     var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
     var a = [];
     var b = new Number;
     var c = 11;
     for (i=0; i<11; i++){
         a[i] = cpf.charAt(i);
         if (i < 9) b += (a[i] * --c);
     }
     if ((x = b % 11) < 2) { a[9] = 0 } else { a[9] = 11-x }
     b = 0;
     c = 11;
     for (y=0; y<10; y++) b += (a[y] * c--);
     if ((x = b % 11) < 2) { a[10] = 0; } else { a[10] = 11-x; }
 
     var retorno = true;
     if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) retorno = false;
 
     return this.optional(element) || retorno;
 
 }, "Informe um CPF válido");
//Tamanho de arquivo
jQuery.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
}, "Arquivo deve ser menor que {0}");

 function inputHandler(masks, max, event) {
	var c = event.target;
	var v = c.value.replace(/\D/g, '');
	var m = c.value.length > max ? 1 : 0;
	VMasker(c).unMask();
	VMasker(c).maskPattern(masks[m]);
	c.value = VMasker.toPattern(v, masks[m]);
}


    $("#cadastro_func").submit(function(e){
        e.preventDefault();
    }).validate({
        errorClass: "error",
        validClass: "valid",

        rules:{
            nome: {
                required: true,
                minlength: 5
            },
            matricula:{
                required: true,
                number: true
            },
            email:{
                required: true,
                email: true
            },
            cpf:{
                required: true,
                number: true,
                cpf: true
            },
            rg:{
                required: true,
                number: true,
                minlength: 10
            },
            ctps: {
                required: true,
                number: true
            },
            tel1:{
                required: true,
                number: true,
                minlength: 11
            },
            tel2:{
                number: true,
                minlength: 11
            },
            banco:{
                required: true
            },
            agencia:{
                required:true,
                number:true
            },
            conta:{
                required: true,
                number: true
            },
            op:{
                required:true
            },
            login:{
                required: true
            },
            senha:{
                required: true,
                minlength: 6
            }//,
           // avatar:{
               // required: false
                //extension: "png,bmp,jpg",
                //filesize: 3145728
            //}
        },

        messages:{
            nome:{
                required: "Digite um nome",
                minlength: "Digite um nome v&aacutelido"
            },
            matricula:{
                required: "Digite uma matricula",
                number: "Somente n&uacutemeros"
            },
            email:{
                required: "Digite um email",
                email: "Digite um email v&aacutelido"

            },
            cpf:{
                required: "Digite um CPF",
                number: "Somente n&uacutemeros",
                cpf: "Digite um CPF v&aacutelido"
            },
            rg:{
                required: "Digite um RG",
                number: "Somente n&uacutemeros",
                minlength: "Digite um RG v&aacutelido"
            },
            ctps:{
                required: "Digite um CTPS",
                number: "Somente n&uacutemeros",
                minlength: "Digite um CTPS v&aacutelido"
            },
            tel1:{
                required: "Digite um n&uacutemeros de telefone",
                number: "Somente n&uacutemeros",
                minlength: "Digite um n&uacutemero v&aacutelido"
            },
            tel2:{
                number: "Somente n&uacutemeros",
                minlength: "Digite um n&uacutemero v&aacutelido"
            },
            banco:{
                required: "Digite um banco"
            },
            agencia:{
                required:" Digite uma agencia",
                number: "Somente n&uacutemeros"
            },
            conta:{
                required:" Digite uma agencia",
                number: "Somente n&uacutemeros"
            },
            op:{
                required: "Digite uma operação"
            },
            login:{
                required: "Digite um login"
            },
            senha:{
                required: "Digite uma senha",
                minlength: "Senha de pelo menos 6 caracteres"
            }//,
           // avatar:{
           //     extension: "Extensão inválida",
            //    silezize: "Arquivo muito grande"

            //}
        },
        submitHandler: function(form, event) { 
            event.preventDefault();
           // form.submit();

            $.post({
                url: form.getAttribute("requisition"),
                data: new FormData($(form)[0]),
                success: function(data){
                    $("#propag_message").html(data);
                },
                cache: false,
                processData: false,
                contentType: false

            });
            //submit via ajax
            //console.log(form.getAttribute("requisition"));
         }
    });