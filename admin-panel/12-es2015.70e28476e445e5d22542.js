(window.webpackJsonp=window.webpackJsonp||[]).push([[12],{Olgc:function(t,e,i){"use strict";i.d(e,"a",(function(){return o}));var a=i("fXoL"),r=i("dNgK");let o=(()=>{class t{constructor(t){this.snackbar=t,this.defaultConfig={horizontalPosition:"right",verticalPosition:"bottom",duration:6e3}}info(t){this.snackbar.open(t,"Okay",Object.assign(Object.assign({},this.defaultConfig),{panelClass:"primary"}))}error(t){this.snackbar.open(t,"Okay",Object.assign(Object.assign({},this.defaultConfig),{panelClass:"warn"}))}warning(t){this.snackbar.open(t,"Okay",Object.assign(Object.assign({},this.defaultConfig),{panelClass:"accent"}))}}return t.\u0275fac=function(e){return new(e||t)(a.cc(r.a))},t.\u0275prov=a.Ob({token:t,factory:t.\u0275fac,providedIn:"root"}),t})()},ahCU:function(t,e,i){"use strict";i.r(e),i.d(e,"OrderManagementModule",(function(){return st}));var a=i("ofXK"),r=i("tyNb"),o=i("fXoL"),c=i("mrSG"),n=i("M9IT"),s=i("+0xr"),l=i("3Pt+"),d=i("VfN6"),b=i("R6Eq"),u=i("0IaG"),p=i("aR35"),m=i("lJxs"),h=i("tk/3");let f=(()=>{class t{constructor(t){this.http=t}getAllOrders(t){return this.http.post(p.e.ORDER[t],{}).pipe(Object(m.a)(t=>{let e=[];return 200===t.code&&(e=t.data),e}))}exportAllOrders(t){return this.http.post(p.e.ORDER[t],{export:1},{responseType:"blob"})}acceptOrder(t){return this.http.post(p.e.ORDER_ACCEPT,t)}assignOrder(t){return this.http.post(p.e.ORDER_ASSIGN,t)}getLocation(t){return this.http.post(p.e.ORDER_LOCATION,t)}}return t.\u0275fac=function(e){return new(e||t)(o.cc(h.b))},t.\u0275prov=o.Ob({token:t,factory:t.\u0275fac,providedIn:"root"}),t})();var g=i("d8ZS"),v=i("Olgc"),S=i("UJjo"),_=i("r1eF"),O=i("SIeF"),y=i("kmnG"),G=i("NFeN"),C=i("d3UM"),w=i("pJxS"),X=i("bTqV"),Y=i("FKr1");function k(t,e){if(1&t&&(o.Yb(0,"mat-option",6),o.Gc(1),o.Xb()),2&t){const t=e.$implicit;o.qc("value",t.carrier_id),o.Gb(1),o.Kc(" ",t.name," | ",t.mobile_number," | Orders:",t.no_of_active_orders," ")}}let T=(()=>{class t{constructor(t,e,i,a,r,o,c){this.dialogRef=t,this.modalData=e,this._formBuilder=i,this._OrderService=a,this._CarrierService=r,this._ToastService=o,this._ProgressBarService=c,this.carriers=[],this.form=this._formBuilder.group({orderid:[this.modalData.order.order_id,l.v.required],carrierid:["",l.v.required]}),r.getAvailableCarriers().subscribe(t=>{this.carriers=t})}ngOnInit(){}submit(t){if(t.preventDefault(),this.form.valid){const t=this.form.value;this._ProgressBarService.show(),this._OrderService.assignOrder(t).subscribe(t=>{200===t.code?(this._ToastService.info(t.message),this.dialogRef.close(!0)):this._ToastService.error(t.message),this._ProgressBarService.hide()})}}}return t.\u0275fac=function(e){return new(e||t)(o.Sb(u.g),o.Sb(u.a),o.Sb(l.d),o.Sb(f),o.Sb(g.a),o.Sb(v.a),o.Sb(S.a))},t.\u0275cmp=o.Mb({type:t,selectors:[["app-assign-order"]],decls:14,vars:5,consts:[["autocomplete","off",3,"formGroup","submit"],[1,"full-width"],["matPrefix","","color","primary",2,"margin-right","20px"],["formControlName","carrierid"],[3,"value",4,"ngFor","ngForOf"],["mat-raised-button","","color","primary",3,"disabled","click"],[3,"value"]],template:function(t,e){1&t&&(o.Yb(0,"form",0),o.gc("submit",(function(t){return e.submit(t)})),o.Yb(1,"app-modal-header"),o.Gc(2),o.Xb(),o.Yb(3,"app-modal-body"),o.Yb(4,"mat-form-field",1),o.Yb(5,"mat-label"),o.Gc(6,"Select carrier (delivery boy)"),o.Xb(),o.Yb(7,"mat-icon",2),o.Gc(8,"local_shipping"),o.Xb(),o.Yb(9,"mat-select",3),o.Ec(10,k,2,4,"mat-option",4),o.Xb(),o.Xb(),o.Xb(),o.Yb(11,"app-modal-action"),o.Yb(12,"button",5),o.gc("click",(function(t){return e.submit(t)})),o.Gc(13,"Assign"),o.Xb(),o.Xb(),o.Xb()),2&t&&(o.qc("formGroup",e.form),o.Gb(2),o.Jc("Assign ",e.modalData.order.transaction_id," (available carrier ",e.carriers.length,") "),o.Gb(8),o.qc("ngForOf",e.carriers),o.Gb(2),o.qc("disabled",e._ProgressBarService.inProgress()||e.form.invalid))},directives:[l.w,l.p,l.i,_.a,O.a,y.b,y.f,G.a,y.g,C.a,l.o,l.g,a.l,w.a,X.b,Y.k],styles:[""]}),t})();var P=i("pxUr");let D=(()=>{let t=class{constructor(t,e){this._OrderService=t,this.modalData=e,this.lat=22.5726,this.lng=88.3639,this.timer=null}ngOnInit(){this.timer=setInterval(()=>{this._OrderService.getLocation({transactionid:this.modalData.order.transaction_id}).pipe(Object(d.b)(this)).subscribe(t=>{var e,i,a,r;(null===(e=null==t?void 0:t.data)||void 0===e?void 0:e.latitude)&&(null===(i=null==t?void 0:t.data)||void 0===i?void 0:i.longitude)&&(this.lat=parseFloat(null===(a=null==t?void 0:t.data)||void 0===a?void 0:a.latitude),this.lng=parseFloat(null===(r=null==t?void 0:t.data)||void 0===r?void 0:r.longitude))})},5e3)}ngOnDestroy(){clearInterval(this.timer)}};return t.\u0275fac=function(e){return new(e||t)(o.Sb(f),o.Sb(u.a))},t.\u0275cmp=o.Mb({type:t,selectors:[["app-locate-order"]],decls:5,vars:6,consts:[[3,"latitude","longitude","zoom"],["iconUrl","./assets/map-marker.png","title","Delivering",3,"latitude","longitude"]],template:function(t,e){1&t&&(o.Yb(0,"app-modal-body"),o.Gc(1),o.Yb(2,"agm-map",0),o.Tb(3,"agm-marker",1),o.Xb(),o.Xb(),o.Tb(4,"app-modal-action")),2&t&&(o.Gb(1),o.Ic(" Order: ",e.modalData.order.transaction_id," "),o.Gb(1),o.qc("latitude",e.lat)("longitude",e.lng)("zoom",14),o.Gb(1),o.qc("latitude",e.lat)("longitude",e.lng))},directives:[O.a,P.b,P.c,w.a],styles:["agm-map[_ngcontent-%COMP%]{height:300px}"]}),t=Object(c.a)([Object(d.a)()],t),t})();var E=i("Qu3c"),x=i("qFsG");function A(t,e){if(1&t&&(o.Yb(0,"mat-option",30),o.Gc(1),o.Xb()),2&t){const t=e.$implicit;o.qc("value",t.key),o.Gb(1),o.Ic(" ",t.value," ")}}function q(t,e){1&t&&(o.Yb(0,"th",31),o.Gc(1," Transaction# "),o.Xb())}function R(t,e){1&t&&(o.Yb(0,"mat-icon",37),o.Gc(1,"verified_user"),o.Xb())}function I(t,e){1&t&&(o.Yb(0,"mat-icon",38),o.Gc(1,"error"),o.Xb())}function L(t,e){1&t&&(o.Yb(0,"mat-icon",39),o.Gc(1,"pending"),o.Xb())}function j(t,e){if(1&t&&(o.Yb(0,"td",32),o.Gc(1),o.Wb(2,33),o.Ec(3,R,2,0,"mat-icon",34),o.Ec(4,I,2,0,"mat-icon",35),o.Ec(5,L,2,0,"mat-icon",36),o.Vb(),o.Xb()),2&t){const t=e.$implicit;o.Gb(1),o.Ic(" ",t.transaction_id," "),o.Gb(1),o.qc("ngSwitch",t.transaction_status),o.Gb(1),o.qc("ngSwitchCase","Success"),o.Gb(1),o.qc("ngSwitchCase","Failed"),o.Gb(1),o.qc("ngSwitchCase","Pending")}}function F(t,e){1&t&&(o.Yb(0,"th",40),o.Gc(1," Address "),o.Xb())}function M(t,e){if(1&t&&(o.Yb(0,"td",41),o.Yb(1,"p",42),o.Gc(2),o.Xb(),o.Xb()),2&t){const t=e.$implicit;o.qc("matTooltip",t.delivery_address),o.Gb(2),o.Hc(t.delivery_address)}}function $(t,e){1&t&&(o.Yb(0,"th",31),o.Gc(1," Order amount "),o.Xb())}function z(t,e){if(1&t&&(o.Yb(0,"td",32),o.Gc(1),o.lc(2,"currency"),o.Xb()),2&t){const t=e.$implicit;o.Gb(1),o.Ic(" ",o.nc(2,1,t.order_amount,"\u20b9")," ")}}function B(t,e){1&t&&(o.Yb(0,"th",31),o.Gc(1," Delivery "),o.Xb())}function V(t,e){if(1&t&&(o.Yb(0,"td",32),o.Gc(1),o.lc(2,"currency"),o.Xb()),2&t){const t=e.$implicit;o.Gb(1),o.Ic(" ",o.nc(2,1,t.delivery_amount,"\u20b9")," ")}}function H(t,e){1&t&&(o.Yb(0,"th",31),o.Gc(1," Tax "),o.Xb())}function N(t,e){if(1&t&&(o.Yb(0,"td",32),o.Gc(1),o.lc(2,"currency"),o.Xb()),2&t){const t=e.$implicit;o.Gb(1),o.Ic(" ",o.nc(2,1,t.tax_amount,"\u20b9")," ")}}function U(t,e){1&t&&(o.Yb(0,"th",31),o.Gc(1," Total "),o.Xb())}function W(t,e){if(1&t&&(o.Yb(0,"td",32),o.Yb(1,"strong"),o.Gc(2),o.lc(3,"currency"),o.Xb(),o.Xb()),2&t){const t=e.$implicit;o.Gb(2),o.Hc(o.nc(3,1,t.transaction_amount,"\u20b9"))}}function J(t,e){1&t&&(o.Yb(0,"th",43),o.Gc(1," Action "),o.Xb())}function K(t,e){if(1&t){const t=o.Zb();o.Yb(0,"button",46),o.gc("click",(function(){o.yc(t);const e=o.kc().$implicit;return o.kc().accept(e)})),o.Yb(1,"mat-icon"),o.Gc(2,"check_circle"),o.Xb(),o.Gc(3," Accept "),o.Xb()}if(2&t){const t=o.kc().$implicit;o.qc("disabled","Failed"==t.transaction_status||"Pending"==t.transaction_status)}}function Q(t,e){if(1&t){const t=o.Zb();o.Yb(0,"button",47),o.gc("click",(function(){o.yc(t);const e=o.kc().$implicit;return o.kc().assign(e)})),o.Yb(1,"mat-icon"),o.Gc(2,"local_shipping"),o.Xb(),o.Gc(3," Assign "),o.Xb()}if(2&t){const t=o.kc().$implicit;o.qc("disabled",1==t.is_assigned||"Failed"==t.transaction_status||"Pending"==t.transaction_status)}}function Z(t,e){if(1&t&&(o.Yb(0,"td",32),o.Ec(1,K,4,1,"button",44),o.Ec(2,Q,4,1,"button",45),o.Xb()),2&t){const t=e.$implicit;o.Gb(1),o.qc("ngIf",!t.is_accepted),o.Gb(1),o.qc("ngIf",t.is_accepted)}}function tt(t,e){1&t&&(o.Yb(0,"th",48),o.Gc(1," Location "),o.Xb())}function et(t,e){if(1&t){const t=o.Zb();o.Yb(0,"td",32),o.Yb(1,"button",49),o.gc("click",(function(){o.yc(t);const i=e.$implicit;return o.kc().locate(i)})),o.Yb(2,"mat-icon"),o.Gc(3,"location_on"),o.Xb(),o.Xb(),o.Xb()}if(2&t){const t=e.$implicit;o.Gb(1),o.qc("disabled",!("Success"==t.transaction_status&&1==t.is_assigned&&1==t.is_accepted))}}function it(t,e){1&t&&o.Tb(0,"tr",50)}function at(t,e){1&t&&o.Tb(0,"tr",51)}let rt=(()=>{let t=class{constructor(t,e,i,a){this.dialog=t,this._ProgressBarService=e,this._OrderService=i,this._ToastService=a,this.orderStatusArr=[{key:"ALL",value:"All"},{key:"NOT_ACCEPTED",value:"Pending"},{key:"ACCEPTED",value:"Accepted"}],this.defaultOrderStatus="ALL",this.orderStatusList=new l.e(this.defaultOrderStatus),this.displayedColumns=["transaction_id","delivery_address","order_amount","delivery_amount","tax_amount","transaction_amount","action","location"],this.length=0,this.pageSize=10,this.pageSizeOptions=[5,10,25,100]}ngOnInit(){this.dataSource=new s.k([]),this.getAllOrders(this.defaultOrderStatus),this.orderStatusList.valueChanges.pipe(Object(d.b)(this)).subscribe(t=>{this.getAllOrders(t)})}getAllOrders(t){this._ProgressBarService.show(),this._OrderService.getAllOrders(t).subscribe(t=>{this.length=t.length,this.dataSource.data=t,this.dataSource.paginator=this.paginator,this._ProgressBarService.hide()})}applyFilter(t){this.dataSource.filter=t.target.value.trim().toLowerCase(),this.dataSource.paginator&&this.dataSource.paginator.firstPage()}accept(t){this.dialog.open(b.a,{width:"600px",disableClose:!0,data:{message:"Accepting the order. Once accepted, assign the order to a delivery boy. Click Ok to continue."}}).afterClosed().subscribe(e=>{e&&(this._ProgressBarService.show(),this._OrderService.acceptOrder({orderid:t.order_id}).subscribe(t=>{this._ToastService.info(t.message),this.orderStatusList.patchValue("ACCEPTED"),this._ProgressBarService.hide()}))})}assign(t){this.dialog.open(T,{width:"600px",disableClose:!0,data:{order:t}}).afterClosed().subscribe(t=>{t&&this.orderStatusList.patchValue("ALL")})}locate(t){this.dialog.open(D,{width:"600px",disableClose:!0,data:{order:t}})}download(t){t.preventDefault(),this._ProgressBarService.show(),this._OrderService.exportAllOrders(this.orderStatusList.value).subscribe(t=>{this._ProgressBarService.hide();const e=document.createElement("a"),i=URL.createObjectURL(t);e.href=i,e.download="orders.xls",e.click(),URL.revokeObjectURL(i)})}};return t.\u0275fac=function(e){return new(e||t)(o.Sb(u.b),o.Sb(S.a),o.Sb(f),o.Sb(v.a))},t.\u0275cmp=o.Mb({type:t,selectors:[["app-order-list"]],viewQuery:function(t,e){var i;1&t&&o.Mc(n.a,!0),2&t&&o.vc(i=o.hc())&&(e.paginator=i.first)},decls:48,vars:9,consts:[[2,"padding","8px"],[1,"row"],[1,"col-xs-12","col-md-4"],[1,"full-width"],["matPrefix","","color","primary",2,"margin-right","20px"],[3,"formControl"],[3,"value",4,"ngFor","ngForOf"],["mat-button","","color","primary","matSuffix","","matTooltip","Download as Excel",3,"click"],[1,"col-xs-12","col-md-4",2,"text-align","right"],["matInput","","placeholder","Search order","autocomplete","off",3,"keyup"],["input",""],[1,"table-container","mat-elevation-z8"],["mat-table","",3,"dataSource"],["matColumnDef","transaction_id","mat-sort-header",""],["mat-header-cell","",4,"matHeaderCellDef"],["mat-cell","",4,"matCellDef"],["matColumnDef","delivery_address","mat-sort-header",""],["mat-header-cell","","style","width: 300px;",4,"matHeaderCellDef"],["mat-cell","",3,"matTooltip",4,"matCellDef"],["matColumnDef","order_amount"],["matColumnDef","delivery_amount"],["matColumnDef","tax_amount"],["matColumnDef","transaction_amount"],["matColumnDef","action"],["mat-header-cell","","style","width: 80px;",4,"matHeaderCellDef"],["matColumnDef","location"],["mat-header-cell","","style","width: 100px;",4,"matHeaderCellDef"],["mat-header-row","",4,"matHeaderRowDef","matHeaderRowDefSticky"],["mat-row","",4,"matRowDef","matRowDefColumns"],[3,"length","pageSize","pageSizeOptions","page"],[3,"value"],["mat-header-cell",""],["mat-cell",""],[3,"ngSwitch"],["class","success","matTooltip","Transaction verified","matTooltipPosition","right",4,"ngSwitchCase"],["class","failed","matTooltip","Transaction failed","matTooltipPosition","right",4,"ngSwitchCase"],["class","pending","matTooltip","Transaction status pending","matTooltipPosition","right",4,"ngSwitchCase"],["matTooltip","Transaction verified","matTooltipPosition","right",1,"success"],["matTooltip","Transaction failed","matTooltipPosition","right",1,"failed"],["matTooltip","Transaction status pending","matTooltipPosition","right",1,"pending"],["mat-header-cell","",2,"width","300px"],["mat-cell","",3,"matTooltip"],[1,"address"],["mat-header-cell","",2,"width","80px"],["mat-button","","color","primary","aria-label","Accept","matTooltip","Accept the order",3,"disabled","click",4,"ngIf"],["mat-button","","color","primary","aria-label","Assign","matTooltip","Assign the order to a delivery boy",3,"disabled","click",4,"ngIf"],["mat-button","","color","primary","aria-label","Accept","matTooltip","Accept the order",3,"disabled","click"],["mat-button","","color","primary","aria-label","Assign","matTooltip","Assign the order to a delivery boy",3,"disabled","click"],["mat-header-cell","",2,"width","100px"],["mat-button","","color","primary","aria-label","Accept","matTooltip","Order live location",3,"disabled","click"],["mat-header-row",""],["mat-row",""]],template:function(t,e){1&t&&(o.Yb(0,"div",0),o.Yb(1,"div",1),o.Yb(2,"div",2),o.Yb(3,"mat-form-field",3),o.Yb(4,"mat-label"),o.Gc(5,"Filter by Order Status"),o.Xb(),o.Yb(6,"mat-icon",4),o.Gc(7,"account_tree"),o.Xb(),o.Yb(8,"mat-select",5),o.Ec(9,A,2,2,"mat-option",6),o.Xb(),o.Xb(),o.Xb(),o.Yb(10,"div",2),o.Yb(11,"button",7),o.gc("click",(function(t){return e.download(t)})),o.Yb(12,"mat-icon"),o.Gc(13,"download"),o.Xb(),o.Gc(14," Download "),o.Xb(),o.Xb(),o.Yb(15,"div",8),o.Yb(16,"mat-form-field"),o.Yb(17,"input",9,10),o.gc("keyup",(function(t){return e.applyFilter(t)})),o.Xb(),o.Xb(),o.Xb(),o.Xb(),o.Xb(),o.Yb(19,"div",11),o.Yb(20,"table",12),o.Wb(21,13),o.Ec(22,q,2,0,"th",14),o.Ec(23,j,6,5,"td",15),o.Vb(),o.Wb(24,16),o.Ec(25,F,2,0,"th",17),o.Ec(26,M,3,2,"td",18),o.Vb(),o.Wb(27,19),o.Ec(28,$,2,0,"th",14),o.Ec(29,z,3,4,"td",15),o.Vb(),o.Wb(30,20),o.Ec(31,B,2,0,"th",14),o.Ec(32,V,3,4,"td",15),o.Vb(),o.Wb(33,21),o.Ec(34,H,2,0,"th",14),o.Ec(35,N,3,4,"td",15),o.Vb(),o.Wb(36,22),o.Ec(37,U,2,0,"th",14),o.Ec(38,W,4,4,"td",15),o.Vb(),o.Wb(39,23),o.Ec(40,J,2,0,"th",24),o.Ec(41,Z,3,2,"td",15),o.Vb(),o.Wb(42,25),o.Ec(43,tt,2,0,"th",26),o.Ec(44,et,4,1,"td",15),o.Vb(),o.Ec(45,it,1,0,"tr",27),o.Ec(46,at,1,0,"tr",28),o.Xb(),o.Xb(),o.Yb(47,"mat-paginator",29),o.gc("page",(function(t){return e.pageEvent=t})),o.Xb()),2&t&&(o.Gb(8),o.qc("formControl",e.orderStatusList),o.Gb(1),o.qc("ngForOf",e.orderStatusArr),o.Gb(11),o.qc("dataSource",e.dataSource),o.Gb(25),o.qc("matHeaderRowDef",e.displayedColumns)("matHeaderRowDefSticky",!0),o.Gb(1),o.qc("matRowDefColumns",e.displayedColumns),o.Gb(1),o.qc("length",e.length)("pageSize",e.pageSize)("pageSizeOptions",e.pageSizeOptions))},directives:[y.b,y.f,G.a,y.g,C.a,l.o,l.f,a.l,X.b,y.h,E.a,x.a,s.j,s.c,s.e,s.b,s.g,s.i,n.a,Y.k,s.d,s.a,a.o,a.p,a.m,s.f,s.h],pipes:[a.d],styles:[".failed[_ngcontent-%COMP%], .pending[_ngcontent-%COMP%], .success[_ngcontent-%COMP%]{font-size:16px;margin-left:6px;cursor:default}.success[_ngcontent-%COMP%]{color:green}.failed[_ngcontent-%COMP%]{color:red}.pending[_ngcontent-%COMP%]{color:orange}.address[_ngcontent-%COMP%]{cursor:default;font-size:10px;text-overflow:ellipsis;max-height:60px;overflow:hidden;line-height:12px;padding:8px}"]}),t=Object(c.a)([Object(d.a)()],t),t})();const ot=[{path:"",component:(()=>{class t{constructor(){}ngOnInit(){}}return t.\u0275fac=function(e){return new(e||t)},t.\u0275cmp=o.Mb({type:t,selectors:[["app-order-management"]],decls:1,vars:0,template:function(t,e){1&t&&o.Tb(0,"app-order-list")},directives:[rt],styles:[""]}),t})()}];let ct=(()=>{class t{}return t.\u0275mod=o.Qb({type:t}),t.\u0275inj=o.Pb({factory:function(e){return new(e||t)},imports:[[r.e.forChild(ot)],r.e]}),t})();var nt=i("PCNd");let st=(()=>{class t{}return t.\u0275mod=o.Qb({type:t}),t.\u0275inj=o.Pb({factory:function(e){return new(e||t)},imports:[[a.c,ct,nt.a.forRoot()]]}),t})()}}]);