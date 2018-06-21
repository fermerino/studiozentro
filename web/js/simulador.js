$(function (){
    if (!editable) {
        $("#configurador").hide();
    }

    //Selección de pestaña del configurador/simulador
    $("ul.tabs-configurador a.nav-link").click(seleccionTabConfigurador);
    $("ul.tabs-simulador a.nav-link").click(seleccionTabSimulador);

    //Cerrado de pestaña
    $(".boton-cerrar").click(cerrarCategoria);

    //ZOOM
    $("#boton-zoom").click(hacerZoom);

    //Selección de vista con los checkboxes
    $("#categoria-silla").find("label").click(seleccionCheckBox);

    //Selección de elementos del configurador
    $("div.select").click(seleccionPiezaOtextura);

    //Confirmar pedido, botón de guardar.
    $("button[type=submit]").click(confirmarPedido);

    $("button.btn-reiniciar").click(reiniciarPrenda);

    $("button.btn-cancelar").click(cancelarPrenda);
    $("button.btn-agregar").click(agregarPrenda);

    $("#simulador").find("a").first().trigger("click");
    cargarConfiguracion();
});

function seleccionTabConfigurador() {
    $("ul.tabs-configurador a").removeClass("active");
    $(this).addClass("active");
    $(".div-configurador").hide();

    var div_id = "#prenda-" + $(this).attr("prenda");
    $(div_id).show();

    $(div_id + " div.select.parte").first().trigger("click");
}

function seleccionTabSimulador(){
    $("ul.tabs-simulador a").removeClass("active");
    $(this).addClass("active");
    $(".div-simulador").hide();

    var div_id = "#categoria-" + $(this).attr("categoria");
    $(div_id).show();

    //Actualizado del configurador:
    $("#configurador").find("ul").hide();

    $("#configurador").find("ul[categoria = '" + $(this).attr("categoria") + "']").show();
    $("#configurador").find("ul[categoria = '" + $(this).attr("categoria") + "'] a").first().trigger("click");

}

function cerrarCategoria() {
    var li = $(this).closest("li")
    var categoria = li.attr("categoria");

    if (!confirm("¿Seguro que desea eliminar la configuración " + categoria + " ?")) {
        return;
    }

    li.remove();
    $(".div-simulador#categoria-" + categoria + " > img").remove();
    $(".div-simulador#categoria-" + categoria).remove();
    $("#simulador li a").first().trigger("click");
    alert("TODO: ELIMINAR TAMBIEN DEL PEDIDO");
}

function hacerZoom() {
    var divs = $(".div-simulador:visible").find(".simulador-parte");

    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        divs
            .css("-webkit-transform", "scale(1)")
            .css("-ms-transform", "scale(1)")
            .css("transform", "scale(1)")
            .css("transition", "1s ease");
    } else {
        $(this).addClass("active");
        divs
            .css("-webkit-transform", "scale(1.4)")
            .css("-ms-transform", "scale(1.4)")
            .css("transform", "scale(1.4)")
            .css("transition", "1s ease");
    }
}

function seleccionCheckBox() {
    if ("ver-saco" === $(this).attr("id")) {
        if ($(this).find("input:checked").length > 0)
            $("div.prenda-saco.simulador-parte").hide(600);
        else
            $("div.prenda-saco.simulador-parte").show(600);
    }
    if ("ver-capota" === $(this).attr("id")) {
        if ($(this).find("input:checked").length > 0)
            $("div.prenda-capota.simulador-parte").hide(1000);
        else
            $("div.prenda-capota.simulador-parte").show(1000);
    }

    if ($("#categoria-silla").find(":checked").length <= 2) {
        $("#categoria-silla .base-completa").hide(600);
        $("#categoria-silla .base").show(600);
        $("div.prenda-funda.simulador-parte").show(600);
    } else {
        $("#categoria-silla .base-completa").show(600);
        $("#categoria-silla .base").hide(600);
        $("div.prenda-funda.simulador-parte").hide(600);
    }

}

function seleccionPiezaOtextura() {
    $(this).closest(".row").children("div.select").removeClass("bordered");
    $(this).addClass("bordered");

    if ($(this).hasClass("textura")) {
        actualizarTexturaZoom($(this).attr("id"));
        var pieza = $(this).closest(".div-configurador").find(".bordered.parte");

        if (pieza.length < 1) {
            return;
        }

        pieza.attr("textura_id", $(this).attr("id"));
        //Realizar cambios en la tabla y simulador
        var divPadre = $(this).closest(".div-configurador");
        actualizarFacturaPieza(divPadre.attr("categoria"), divPadre.attr("prenda"), $(this), pieza);
    }

    if ($(this).hasClass("parte")) {
        var textura_id = $(this).attr("textura_id");
        actualizarTexturaZoom(textura_id);
        $("div.div-configurador .textura").removeClass("bordered");

        if (textura_id.length > 1) {
            $(this).closest(".div-configurador").find("#" + textura_id).addClass("bordered");
        }
    }
}

function actualizarFacturaPieza(categoria, prenda, textura, pieza) {

    var fila    = pieza.attr("id").split("-")[1];
    var precio  = parseFloat(textura.attr("precio"));
    var grupo   = parseFloat(textura.attr("grupo"));
    var sumaP   = 0;
    var sumaT   = 0;
    var max;
    var detalle = $("table[categoria=" + categoria + "][prenda=" + prenda + "]");

    detalle.show();
    detalle.find("#row-" + fila).remove();

    detalle.find("tbody:last").prev().append(
        "<tr id='row-" + fila + "' class='row-elemento' pieza_id="+fila+" textura_id="+textura.attr("id").split("-")[1]+">" +
        "<td><em>" + pieza.attr("name") + "</em></td>" +
        "<td><em>" + textura.attr("name") + " (" + precio + "/ud)</em></td>" +
        "<td class='text-right subtotal' grupo='" + grupo + "'>" + precio + "</td>" +
        "</tr>"
    );

    max = 0;
    sumaP = 0;
    detalle.find("td.subtotal").each(function(){
        var maxGroup = parseInt($(this).attr('grupo'));
        if(maxGroup > max){
            max = maxGroup;
            sumaP = parseFloat($(this).text()||0,10); //numero de la celda 3
        }
    }).show();

    $("#div-detalle").find("table").not("cancelado").find(".total-parcial").each(function() {
        sumaT += parseFloat($(this).text()||0,10); //numero de la celda 3
    });

    detalle.find(".total-parcial").html(sumaP + " €");
    $("#total").html(sumaT.toFixed(2) + " €");

    var json        = {};
    json.pieza_id  = fila;
    json.textura_id = textura.attr("id").split("-")[1];
    json.carro_id   = $("#categoria-silla").attr("carro_id");

    $.ajax({
        type: "POST",
        url: get_data_route,
        data: 'data='+JSON.stringify(json),
        success: function(result) {
            if (result["imagen"].length > 1) {
                $("#" + result["simulador_id"] + " img").attr('src', result["imagen"]);
            } else {
                alert("Textura no renderizable");
            }
        }
    });
}

function actualizarTexturaZoom(textura) {
    var json        = {};
    json.textura_id = textura.split("-")[1];

    if (typeof json.textura_id === 'undefined') {
        return;
    }
    $.ajax({
        type: "POST",
        url: get_img_textura,
        data: 'data='+JSON.stringify(json),
        success: function(result) {
            if (result["imagen"].length > 1) {
                $(".zoom-textura img").css('background-image', "url(" + result['imagen'] + ")");
            } else {
                alert("Textura no renderizable");
            }
        }
    });
}

function confirmarPedido() {
    var json = [];
    var email = $("#email").val();

    if (email.length < 3) {
        alert("Debe de introducir un nombre o email válido");
        return;
    }

    $("#div-detalle").find("table:visible tr").each(function () {
        if ($(this).attr("pieza_id") !== undefined) {
            json.push({
                "pieza_id" : $(this).attr("pieza_id"),
                "textura_id": $(this).attr("textura_id"),
                "email"     : email
            });
        }
    });

    $.ajax({
        type: "POST",
        url: guardar_pedido,
        contentType: 'application/json',
        data: JSON.stringify(json),
        success: function(result) {
            alert(result);
        }
    });
}

function reiniciarPrenda() {
    var categoria = $(this).attr("categoria");
    var prenda = $(this).attr("prenda");
    $.each(configuracion[categoria][prenda], function(pieza, textura) {
        $("#prenda-" + prenda + " #pieza-" + pieza).trigger("click");
        $("#prenda-" + prenda + " #textura-" + textura).trigger("click");
    });
}

function cancelarPrenda() {
    var prenda      = $(this).attr("prenda");
    var categoria   = $(this).attr("categoria");

    if (!confirm("¿Cancelar la prenda " + prenda + " ?")) {
        return;
    }

    $("#categoria-" + categoria + " .prenda-" + prenda + ".simulador-parte").css("opacity", "0.2");
    $("#prenda-" + prenda + " .row").hide();
    $("#prenda-" + prenda + "-off").show();

    $("#div-detalle").find("table[prenda=" + prenda + "]").hide();
}

function agregarPrenda() {
    var prenda      = $(this).attr("prenda");
    var categoria   = $(this).attr("categoria");

    if (!confirm("¿Agregar la prenda " + prenda + " ?")) {
        return;
    }

    $("#categoria-" + categoria + " .prenda-" + prenda + ".simulador-parte").css("opacity", "1.0");
    $("#prenda-" + prenda + "-off").hide();
    $("#prenda-" + prenda + " .row").show();

    $("#div-detalle").find("table[prenda=" + prenda + "]").show();
}

function cargarConfiguracion() {
    $.each(configuracion, function (categoria, prendas) {
        $.each(prendas, function (prenda, par) {
            $.each(par, function(pieza, textura) {
                $("#prenda-" + prenda + " #pieza-" + pieza).trigger("click");
                $("#prenda-" + prenda + " #textura-" + textura).trigger("click");
            });
        });
    });


    $(".tabs-configurador a").first().trigger("click");

    //Para que no se vea fea la funda
    $(".prenda-funda").hide().delay(3000);
}

function maxWindow() {
    window.moveTo(0, 0);


    if (document.all) {
        top.window.resizeTo(screen.availWidth, screen.availHeight);
    }

    else if (document.layers || document.getElementById) {
        if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth) {
            top.window.outerHeight = screen.availHeight;
            top.window.outerWidth = screen.availWidth;
        }
    }
}