
swefminer = {

    dialogue                  : null
   ,initialised               : null
   ,buttons                   : null
   ,log                       : console.log

   ,_init : function ( ) {
        if (this.initialised) {
            return;
        }
        if (swef==undefined || typeof(swef)!='object') {
            console.log ('Object swef was not found');
            return;
        }
//        this.buttons                    = document.getElementsByClassName ('button');
//        this.abc             = document.getElementById ('abc');
//        this.buttonX.addEventListener  ('click', this.x);
        this.initialised                = true;
        this.log ('swefminer initialised');
    }

   ,tableSaveCallback : function (xhr,args) {
        swef.unwait ();
        var form            = args[0];
        if (xhr.status!=200) {
            console.log ('API HTTP status: '+xhr.status);
            swef.notify ('Failed to save table info');
            return;
        }
        try {
            var json    = JSON.parse (xhr.responseText);
        }
        catch (e) {
            console.log ('Could not parse JSON:');
            console.log (xhr.responseText);
            return;
        }
        console.log (json);
        if (json.results[0].status!=200) {
            if (action=='checkout') {
                console.log ("Procedure \\Swef\\swefMiner::tableUpdate() status: "+json.results[0].status);
                swef.notify ('Could not save: 'json.results[0].status);
            }
            else {
                console.log ("Procedure \\Swef\\swefMiner::tableUpdate() status: "+json.results[0].status);
                swef.notify ('Failed to check in this language/version');
            }
        }
    }

   ,esc : function (str) {
        return swef.escapeHTML (str);
    }

   ,tableSave : function (form) {
        var json    = {
            procedures    : [
                [ "\\Swef\\swefMiner::apiTableUpdate", form.item_uuid.value, active, order ]
            ]
        };
        swef.apiSend (json,this.tableSaveCallback,[form]);
        swef.wait ();
    }

   ,parserSet : function (pFunc) {
        if (pFunc==null) {
            pFunc                           = this.parseMarkdown;
        }
        if (typeof(pFunc)!='function') {
            console.log ('"'+pFunc+'" is not a function');
            return false;
        }
        this.esc            = swef.escapeHTML;
        this.parserFunction = (
            function ( ) {
                console.log ('Parse using '+pFunc.name+'()');
                this.parserOutput.innerHTML     = pFunc (this);
                this.parserTitleOut.innerHTML   = this.esc (this.parserTitleIn.value);
                this.parserInputScrollTopMax    = this.parserInput.scrollHeight;
                this.parserInputScrollTopMax   += this.parserInput.style.borderTopWidth;
                this.parserInputScrollTopMax   -= this.parserInput.offsetHeight;
                this.parserOutputScrollTopMax   = this.parserOutput.scrollHeight;
                this.parserOutputScrollTopMax  += this.parserOutput.style.borderTopWidth;
                this.parserOutputScrollTopMax  -= this.parserOutput.offsetHeight;
            }
        ).bind (this);
        this.parserRendered = (
            function (ev) {
                console.log ('Setting view to rendered HTML');
                this.viewMode (false);
                this.parserFunction ();
                ev.preventDefault ();
            }
        ).bind (this);
        this.parserSource = (
            function (ev) {
                console.log ('Setting view to source code');
                this.viewMode (true);
                this.parserFunction ();
                ev.preventDefault ();
            }
        ).bind (this);
        this.parserScrollInput = (
            function ( ) {
                this.parserInput.scrollTop     = Math.round (this.parserInputScrollTopMax*this.parserOutput.scrollTop/this.parserOutputScrollTopMax);
            }
        ).bind (this);
        this.parserScrollOutput = (
            function ( ) {
                this.parserOutput.scrollTop    = Math.round (this.parserOutputScrollTopMax*this.parserInput.scrollTop/this.parserInputScrollTopMax);
            }
        ).bind (this);
        this.checkOut = (
            function (evt) {
                var elmt        = evt.currentTarget;
                var action      = elmt.getAttribute ('data-action');
                var itemUUID    = elmt.form.item_uuid.value;
                var lng         = this.parserSelectLanguage.options[this.parserSelectLanguage.selectedIndex];
                var vn          = this.parserSelectVersion.options[this.parserSelectVersion.selectedIndex];
                if (action='checkout') {
                    var json    = {
                        procedures    : [
                            [ "\\Swef\\swefminer::apiLanguageCheckOut", swef.userUUID, itemUUID, lng, vn ]
                        ]
                    };
                    elmt.setAttribute ('data-action','checkin');
                }
                else {
                    var json    = {
                        procedures    : [
                            [ "\\Swef\\swefminer::apiLanguageCheckIn", swef.userUUID, itemUUID, lng, vn ]
                        ]
                    };
                    elmt.setAttribute ('data-action','checkout');
                }
                var val   = elmt.nodeValue;
                var nVal  = elmt.getAttribute ('data-next-value');
                elmt.setAttribute ('data-next-value',val);
                elmt.nodeValue = nVal;
                swef.apiSend (json,this.checkOutCallback,[elmt.form,action]);
                swef.wait ();

            }
        ).bind (this);
        this.parserButtonSave.addEventListener (
            'click'
           ,function ( ) { window.onbeforeunload = null; }
        );
    }

}

