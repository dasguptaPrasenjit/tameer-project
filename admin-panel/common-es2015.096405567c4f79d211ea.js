(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{"2hTz":function(t,e,r){"use strict";r.d(e,"a",(function(){return u}));var n=r("q3Kh"),s=r("aR35"),o=r("HDdC"),p=r("fXoL"),a=r("tk/3");let u=(()=>{class t{constructor(t){this.http=t}register(t){return this.http.post(s.e.REGISTER,t)}signIn(t){return this.http.post(s.e.LOGIN,t)}getAllVendors(){return this.http.post(s.e.VENDORS,{}).pipe(Object(n.map)(t=>200===t.status?t.data:[]))}removeVendor(t){return this.http.post(s.e.DELETE_VENDOR,t)}addUser(t){return new o.a}updateUser(t,e){return this.http.post(s.e.VENDOR_UPDATE+"/"+t,e)}updateUserProp(t,e){return new o.a}getEmailBySignupToken(t){return new o.a}getUserByEmail(t){return new o.a}getVendorByCategoryId(t){return this.http.post(s.e.VENDORBYCATEGORYID,t)}}return t.\u0275fac=function(e){return new(e||t)(p.cc(a.b))},t.\u0275prov=p.Ob({token:t,factory:t.\u0275fac,providedIn:"root"}),t})()}}]);