async(page)=>{
 const ctx=await page.context().browser().newContext({ignoreHTTPSErrors:true,viewport:{width:375,height:900}}),p=await ctx.newPage();
 await p.goto('https://koalainsulation.local/traverse-city',{waitUntil:'domcontentloaded'});
 await p.evaluate(()=>{document.querySelectorAll('chat-widget').forEach(e=>e.remove());const host=document.createElement('chat-widget');const root=host.attachShadow({mode:'open'});root.innerHTML='<style>.lc_text-widget{position:fixed;right:20px;bottom:20px;z-index:99999999;width:301px;height:150px;background:white}.lc_text-widget button{position:absolute;right:0;bottom:0;width:58px;height:58px}</style><div class="lc_text-widget"><button>Open chat fixture</button></div>';root.querySelector('button').onclick=()=>host.dataset.clicked='true';document.body.append(host);});
 await p.waitForTimeout(100);
 const r=await p.locator('chat-widget').evaluate(e=>e.shadowRoot.querySelector('.lc_text-widget').getBoundingClientRect().toJSON());
 if(r.bottom>840)throw Error('Chat overlaps the bottom 60px quote bar: '+r.bottom);
 await p.locator('[data-sidebar-open]').click({timeout:3000});
 if(!await p.locator('#gform_12').isVisible())throw Error('Form did not open');
 await p.waitForTimeout(350);await p.locator('#gform_submit_button_12').scrollIntoViewIfNeeded();
 const clear=await p.locator('#gform_submit_button_12').evaluate(e=>{const r=e.getBoundingClientRect();const hit=document.elementFromPoint(r.x+r.width/2,r.y+r.height/2);return hit===e||e.contains(hit);});
 if(!clear)throw Error('Form submit hit-test failed: '+JSON.stringify(await p.locator('#gform_submit_button_12').evaluate(e=>{const r=e.getBoundingClientRect();return {rect:r.toJSON(),hit:document.elementFromPoint(r.x+r.width/2,r.y+r.height/2)?.outerHTML.slice(0,400)}})));await p.keyboard.press('Escape');
 await p.locator('chat-widget').getByRole('button',{name:'Open chat fixture'}).click();
 if(await p.locator('chat-widget').getAttribute('data-clicked')!=='true')throw Error('Chat no longer clickable');
 await p.setViewportSize({width:1440,height:900});await p.waitForTimeout(100);
 const desktop=await p.locator('chat-widget').evaluate(e=>getComputedStyle(e.shadowRoot.querySelector('.lc_text-widget')).bottom);
 if(desktop!=='20px')throw Error('Desktop chat position changed');await ctx.close();return 'Mobile quote and chat clickable; desktop chat offset preserved';
}
