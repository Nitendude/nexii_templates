
// ===== DATA =====
let invData = [];

let soData = [];

let arData = [];

let poData = [];

let apData = [];

let glData = [];

let custData = [];

const firstNames = ['Adrian','Sophia','Marcus','Daniel','Alyssa','Ethan','Camille','Nico','Liam','Angela','Patricia','Rafael','Isabel','Gabriel','Bea','Carlo','Mikaela','Andre','Katrina','Paolo','Janine','Luis','Hazel','Marco','Sofia','Arvin','Clarissa','Martin','Bianca','Noel','Lara','Enrico','Janelle','Vince','Kyla','Mateo','Trisha','Rodel','Andrea','Jericho','Pauline','Aldrin','Mariel','Julian','Giselle','Kenneth','Marian','Alvin','Nina','Cedric','Regina','Jomar','Leila','Harvey','Christine','Anton','Rochelle','Dennis','Jasmine','Rowena','Miguel','Rosario','Grace','Patrick','Victor','Renzo','Maureen','Cesar','Elaine','Ramon','Cassandra','Joshua','Monica','Nathan','Rhea','Diego','Irene','Gerald','Mara','Olivia','Noah','Emma','Lucas','Mia','Henry','Ava','Mason','Chloe','Logan','Sienna','Elliot','Amelia','Owen','Naomi','Caleb','Freya','Miles','Zoe','Evan'];
const lastNames = ['Villanueva','Reyes','Dela Cruz','Fernandez','Ramos','Navarro','Santos','Bautista','Mendoza','Garcia','Soriano','Aquino','Castillo','Mercado','Santiago','Flores','Valdez','Salazar','Domingo','Cordero','Manalo','Ignacio','Abad','Lacson','Tolentino','Uy','De Leon','Magtibay','Tuazon','Evangelista','Panganiban','Robles','Javier','Dizon','Corpuz','Gonzales','Villamor','Samson','Lim','Yap','Francisco','Pineda','Tan','Cruz','Sy','Villar','Padilla','Angeles','Ong','Velasco','Alcantara','Macapagal','Chua','Ocampo','Bernal','Pascual','Castro','Imperial','Ferrer','Buenaventura','Hernandez','Rivera','Torres','Diaz','Bennett','Walker','Williams','Chen','Patel','Nguyen','Tanaka','Morgan','Cooper','Anderson','Thompson','Kim','Sullivan','Brooks','Carter','Murphy'];
const usedNames = new Set();
function personName(seed){for(let step=0;step<firstNames.length*lastNames.length;step++){const name=`${firstNames[(seed+step)%firstNames.length]} ${lastNames[((seed*7)+step*13)%lastNames.length]}`;if(!usedNames.has(name)){usedNames.add(name);return name}}return `Demo Person ${seed}`}
function pad(n,w=3){return String(n).padStart(w,'0')}
function phone(seed){return `09${17+(seed%10)}-${pad((100+seed*37)%1000)}-${pad((200+seed*53)%1000)}`}
function dateLong(seed){return `May ${pad(12-(seed%12),2)}, 2026`}
function dateShort(seed){return `May ${pad(12-(seed%12),2)}`}
function isoDate(seed){return `2026-04-${pad(1+(seed%28),2)}`}

const branchBases = [
['Manila Main','NCR','Rizal Avenue, Santa Cruz','Manila','Metro Manila'],['Quezon City North','NCR','Quezon Avenue','Quezon City','Metro Manila'],['Makati Ayala','NCR','Gil Puyat Avenue','Makati','Metro Manila'],['Taguig BGC','NCR','32nd Street, Bonifacio Global City','Taguig','Metro Manila'],['Pasig Ortigas','NCR','Ortigas Avenue','Pasig','Metro Manila'],
['Pasay EDSA','NCR','EDSA Extension','Pasay','Metro Manila'],['Mandaluyong Shaw','NCR','Shaw Boulevard','Mandaluyong','Metro Manila'],['Caloocan Monumento','NCR','Rizal Avenue Extension','Caloocan','Metro Manila'],['Valenzuela Karuhatan','NCR','MacArthur Highway','Valenzuela','Metro Manila'],['Paranaque Sucat','NCR','Dr. A. Santos Avenue','Paranaque','Metro Manila'],
['Las Pinas Alabang-Zapote','NCR','Alabang-Zapote Road','Las Pinas','Metro Manila'],['Muntinlupa Alabang','NCR','National Road, Alabang','Muntinlupa','Metro Manila'],['Marikina Sumulong','NCR','Sumulong Highway','Marikina','Metro Manila'],['San Juan Greenhills','NCR','Ortigas Avenue','San Juan','Metro Manila'],['Navotas North Bay','NCR','North Bay Boulevard','Navotas','Metro Manila'],
['Malabon C-4','NCR','C-4 Road','Malabon','Metro Manila'],['Antipolo Masinag','Luzon','Marcos Highway','Antipolo','Rizal'],['Bacoor Molino','Luzon','Molino Boulevard','Bacoor','Cavite'],['Dasmarinas Governor Drive','Luzon','Governor Drive','Dasmarinas','Cavite'],['Imus Aguinaldo','Luzon','Aguinaldo Highway','Imus','Cavite'],
['General Trias Manggahan','Luzon','Governor Ferrer Drive','General Trias','Cavite'],['Cavite City San Roque','Luzon','P. Burgos Avenue','Cavite City','Cavite'],['Trece Martires Capitol','Luzon','Capitol Road','Trece Martires','Cavite'],['Santa Rosa Balibago','Luzon','Santa Rosa-Tagaytay Road','Santa Rosa','Laguna'],['Calamba Crossing','Luzon','National Highway','Calamba','Laguna'],
['San Pablo Maharlika','Luzon','Maharlika Highway','San Pablo','Laguna'],['Lipa Ayala Highway','Luzon','Ayala Highway','Lipa','Batangas'],['Batangas City Diversion','Luzon','Diversion Road','Batangas City','Batangas'],['Lucena Iyam','Luzon','Dalahican Road','Lucena','Quezon'],['Tayabas Diversion','Luzon','Tayabas-Lucena Road','Tayabas','Quezon'],
['San Fernando Pampanga','Luzon','Jose Abad Santos Avenue','San Fernando','Pampanga'],['Angeles Balibago','Luzon','MacArthur Highway','Angeles','Pampanga'],['Mabalacat Dau','Luzon','Dau Access Road','Mabalacat','Pampanga'],['Olongapo Rizal','Luzon','Rizal Avenue','Olongapo','Zambales'],['Balanga Capitol','Luzon','Capitol Drive','Balanga','Bataan'],
['Malolos Crossing','Luzon','MacArthur Highway','Malolos','Bulacan'],['Meycauayan Camalig','Luzon','MacArthur Highway','Meycauayan','Bulacan'],['San Jose del Monte','Luzon','Quirino Highway','San Jose del Monte','Bulacan'],['Cabanatuan Maharlika','Luzon','Maharlika Highway','Cabanatuan','Nueva Ecija'],['Tarlac City San Roque','Luzon','MacArthur Highway','Tarlac City','Tarlac'],
['Dagupan Downtown','Luzon','A.B. Fernandez Avenue','Dagupan','Pangasinan'],['Urdaneta MacArthur','Luzon','MacArthur Highway','Urdaneta','Pangasinan'],['San Fernando La Union','Luzon','Quezon Avenue','San Fernando','La Union'],['Baguio Session','Luzon','Session Road','Baguio','Benguet'],['Tuguegarao Carig','Luzon','Cagayan Valley Road','Tuguegarao','Cagayan'],
['Ilagan Centro','Luzon','Maharlika Highway','Ilagan','Isabela'],['Santiago Balintocatoc','Luzon','Pan-Philippine Highway','Santiago','Isabela'],['Laoag Bacarra','Luzon','Bacarra Road','Laoag','Ilocos Norte'],['Vigan Bantay','Luzon','Quezon Avenue','Vigan','Ilocos Sur'],['Legazpi Rizal','Luzon','Rizal Street','Legazpi','Albay'],
['Naga Magsaysay','Luzon','Magsaysay Avenue','Naga','Camarines Sur'],['Sorsogon Diversion','Luzon','Maharlika Highway','Sorsogon City','Sorsogon'],['Virac San Roque','Luzon','Catanduanes Circumferential Road','Virac','Catanduanes'],['Puerto Princesa Rizal','Luzon','Rizal Avenue','Puerto Princesa','Palawan'],['Calapan Nautical','Luzon','Nautical Highway','Calapan','Oriental Mindoro'],
['Cebu City Colon','Visayas','Colon Street','Cebu City','Cebu'],['Mandaue North Reclamation','Visayas','North Reclamation Area','Mandaue','Cebu'],['Lapu-Lapu Basak','Visayas','M.L. Quezon National Highway','Lapu-Lapu','Cebu'],['Talisay Cebu Tabunok','Visayas','Natalio Bacalso Avenue','Talisay','Cebu'],['Danao Central','Visayas','Central Nautical Highway','Danao','Cebu'],
['Toledo Lutopan','Visayas','Toledo-Tabuelan Road','Toledo','Cebu'],['Tagbilaran CPG','Visayas','Carlos P. Garcia Avenue','Tagbilaran','Bohol'],['Dumaguete Real','Visayas','Real Street','Dumaguete','Negros Oriental'],['Bacolod Lacson','Visayas','Lacson Street','Bacolod','Negros Occidental'],['Silay Rizal','Visayas','Rizal Street','Silay','Negros Occidental'],
['Kabankalan Crossing','Visayas','National Highway','Kabankalan','Negros Occidental'],['Iloilo Diversion','Visayas','Benigno Aquino Avenue','Iloilo City','Iloilo'],['Roxas Pueblo','Visayas','Arnaldo Boulevard','Roxas City','Capiz'],['Kalibo Andagao','Visayas','Roxas Avenue','Kalibo','Aklan'],['Tacloban Real','Visayas','Real Street','Tacloban','Leyte'],
['Ormoc Aviles','Visayas','Aviles Street','Ormoc','Leyte'],['Maasin Tunga-Tunga','Visayas','Tomas Oppus Street','Maasin','Southern Leyte'],['Catbalogan Pier','Visayas','Rizal Avenue','Catbalogan','Samar'],['Davao Bajada','Mindanao','J.P. Laurel Avenue','Davao City','Davao del Sur'],['Tagum Apokon','Mindanao','Apokon Road','Tagum','Davao del Norte'],
['Panabo Gredu','Mindanao','Panabo Wharf Road','Panabo','Davao del Norte'],['Digos Tres de Mayo','Mindanao','Rizal Avenue','Digos','Davao del Sur'],['Mati Dahican','Mindanao','Dahican Road','Mati','Davao Oriental'],['General Santos Pioneer','Mindanao','Pioneer Avenue','General Santos','South Cotabato'],['Koronadal Alunan','Mindanao','Alunan Avenue','Koronadal','South Cotabato'],
['Kidapawan Quezon','Mindanao','Quezon Boulevard','Kidapawan','Cotabato'],['Cotabato City Sinsuat','Mindanao','Sinsuat Avenue','Cotabato City','Maguindanao del Norte'],['Cagayan de Oro Agora','Mindanao','Claro M. Recto Avenue','Cagayan de Oro','Misamis Oriental'],['Iligan Tibanga','Mindanao','Macapagal Avenue','Iligan','Lanao del Norte'],['Malaybalay Sayre','Mindanao','Sayre Highway','Malaybalay','Bukidnon'],
['Valencia Bukidnon','Mindanao','Sayre Highway','Valencia','Bukidnon'],['Butuan J.C. Aquino','Mindanao','J.C. Aquino Avenue','Butuan','Agusan del Norte'],['Surigao Borromeo','Mindanao','Borromeo Street','Surigao City','Surigao del Norte'],['Tandag Capitol','Mindanao','Capitol Road','Tandag','Surigao del Sur'],['Zamboanga Canelar','Mindanao','Gov. Camins Avenue','Zamboanga City','Zamboanga del Sur'],
['Dipolog Turno','Mindanao','Dipolog-Oroquieta Road','Dipolog','Zamboanga del Norte'],['Pagadian F.S. Pajares','Mindanao','F.S. Pajares Avenue','Pagadian','Zamboanga del Sur'],['Isabela Basilan','Mindanao','Valderrosa Street','Isabela City','Basilan'],['Marawi MSU','Mindanao','Amai Pakpak Avenue','Marawi','Lanao del Sur'],['Ozamiz Port Road','Mindanao','Port Road','Ozamiz','Misamis Occidental'],
['Gingoog National Highway','Mindanao','Butuan-Cagayan de Oro-Iligan Road','Gingoog','Misamis Oriental'],['Bislig Mangagoy','Mindanao','Espiritu Street','Bislig','Surigao del Sur'],['Bayugan Rotunda','Mindanao','National Highway','Bayugan','Agusan del Sur'],['Jolo Capitol','Mindanao','Scott Road','Jolo','Sulu'],['Bongao Port','Mindanao','Bongao-Pagasinan Road','Bongao','Tawi-Tawi']
];
const branchData = branchBases.map((b,i)=>({name:b[0],region:b[1],address:`${b[2]}, ${b[3]}, ${b[4]}`,city:b[3],province:b[4],manager:personName(i),contact:phone(i),inventory:0,sold:0,cashFund:35000+((i*7391)%95000),status:i%19===0?'Low Fund':i%23===0?'Low Stock':'Normal'}));
const catalog = [
  {brand:'Honda',model:'Click 125i',colors:['Pearl White','Matte Black','Vivid Red'],price:89900},{brand:'Honda',model:'Beat',colors:['Red','Black','Blue'],price:67900},{brand:'Honda',model:'ADV 160',colors:['Morion Gray','Pearl White','Matte Red'],price:148900},{brand:'Honda',model:'PCX 160',colors:['Pearl White','Matte Blue','Black'],price:134900},
  {brand:'Yamaha',model:'Mio i 125',colors:['Matte Blue','Matte Black','White'],price:78900},{brand:'Yamaha',model:'NMAX 155',colors:['Blue','Gray','Black'],price:132900},{brand:'Yamaha',model:'Aerox 155',colors:['Nardo Gray','Racing Blue','Black'],price:138900},{brand:'Yamaha',model:'Sniper 155',colors:['Racing Red','Cyan','Black'],price:125900},
  {brand:'Kawasaki',model:'Barako II',colors:['Red','Black','Green'],price:68000},{brand:'Kawasaki',model:'Rouser NS160',colors:['Ebony Black','Metallic Gray','Red'],price:118900},{brand:'Suzuki',model:'Raider R150',colors:['Gray','Blue','Red'],price:111900},{brand:'Suzuki',model:'Burgman Street 125',colors:['Pearl White','Matte Black','Silver'],price:82900},
  {brand:'TVS',model:'Apache RTR 160',colors:['Matte Black','Red','White'],price:124900},{brand:'TVS',model:'Dazz 110',colors:['Blue','White','Black'],price:59900}
];
const suppliers = [{name:'Honda Philippines',models:'Click 125i, Beat, ADV 160, PCX 160'},{name:'Yamaha Motor PH',models:'Mio i 125, NMAX 155, Aerox 155, Sniper 155'},{name:'Kawasaki Motors PH',models:'Barako II, Rouser NS160'},{name:'Suzuki Philippines',models:'Raider R150, Burgman Street 125'},{name:'TVS Motor PH',models:'Apache RTR 160, Dazz 110'},{name:'Metro Accessories Inc.',models:'Helmets, boxes, riding gear'}];

custData = Array.from({length:360},(_,i)=>{const branch=branchData[(i*11)%branchData.length];const units=1+(i%8===0?1:0)+(i%31===0?1:0);return {id:`CUST-${pad(i+1)}`,name:personName(180+i),contact:phone(200+i),address:`${branch.city}, ${branch.province}`,branch:branch.name,units,total:units*(65000+((i*977)%90000)),status:i%29===0?'Overdue':'Active'}});
invData = Array.from({length:1248},(_,i)=>{const unit=catalog[(i*5)%catalog.length],branch=branchData[(i*7)%branchData.length],color=unit.colors[i%unit.colors.length];const status=i%6===0?'Sold':i%9===0?'Reserved':'Available';return {id:`UNIT-${pad(i+1,4)}`,brand:unit.brand,model:unit.model,color,engine:`ENG-2026-${pad(10000+i*17,5)}`,chassis:`CHS-2026-${pad(20000+i*19,5)}`,branch:branch.name,status,price:unit.price,date:isoDate(i)}});
soData = Array.from({length:420},(_,i)=>{const customer=custData[i%custData.length],unit=catalog[(i*3)%catalog.length],color=unit.colors[(i+1)%unit.colors.length];const mode=i%5===0?'Bank Financing':i%3===0?'Cash':'Installment';const status=i%41===0?'Cancelled':i%9===0?'Pending':i%7===0?'Processing':'Completed';const dp=mode==='Cash'?unit.price:mode==='Bank Financing'?Math.round(unit.price*.3):Math.round(unit.price*.2);return {no:`SO-2026-${pad(720-i,4)}`,date:dateLong(i),customer:customer.name,model:`${unit.brand} ${unit.model} (${color})`,srp:unit.price,dp,mode,branch:customer.branch,status}});
arData = soData.filter(r=>r.mode!=='Cash'&&r.status!=='Cancelled').slice(0,250).map((r,i)=>{const status=i%17===0?'Paid':i%11===0?'Overdue':'Current';const balance=status==='Paid'?0:r.srp-r.dp-Math.round((i%6)*r.srp*.055);return {no:`AR-2026-${pad(300+i)}`,customer:r.customer,model:r.model.replace(/\s\(.+\)$/,''),total:r.srp,balance:Math.max(0,balance),monthly:status==='Paid'?0:Math.round((r.srp-r.dp)/24),due:status==='Paid'?'—':dateLong(i+3),overdue:status==='Overdue'?5+(i%21):0,branch:r.branch,status}});
poData = Array.from({length:180},(_,i)=>{const supplier=suppliers[i%suppliers.length],branch=branchData[(i*13)%branchData.length],qty=12+((i*7)%46);return {no:`PO-2026-${pad(280-i,4)}`,date:dateLong(i+1),supplier:supplier.name,model:supplier.models,qty,amount:qty*(62000+((i*311)%83000)),delivery:dateLong(i+8),branch:branch.name,status:i%13===0?'Cancelled':i%5===0?'Pending':i%3===0?'Approved':'Delivered'}});
apData = poData.slice(0,140).map((r,i)=>{const paid=i%6===0?0:i%4===0?Math.round(r.amount*.45):r.amount;const due=r.amount-paid;return {inv:`INV-2026-${pad(510-i)}`,vendor:r.supplier,desc:`${r.model} delivery batch (${r.no})`,amount:r.amount,paid,due,invDate:dateLong(i+2),dueDate:dateLong(i+18),status:due===0?'Paid':i%6===0?'Overdue':'Partial'}});
glData = Array.from({length:200},(_,i)=>{const so=soData[i%soData.length],ar=arData[i%arData.length],ap=apData[i%apData.length];const variants=[{account:'Cash in Bank - BPI / Accounts Receivable',desc:`Installment payment received - ${ar.customer}`,amount:ar.monthly||ar.total,type:'Payment'},{account:'Accounts Payable / Cash in Bank - BDO',desc:`Supplier payment - ${ap.vendor} (${ap.inv})`,amount:Math.max(ap.paid,38500),type:'Purchase'},{account:'Cash on Hand / Sales Revenue',desc:`Cash sales deposit - ${so.branch}`,amount:so.srp,type:'Sales'},{account:'Operating Expense / Cash Fund',desc:`Branch activity expense - ${branchData[(i*5)%branchData.length].name}`,amount:1800+((i*257)%18000),type:'Adjustment'}];const v=variants[i%variants.length];return {date:dateShort(i),ref:`JE-202605${pad(12-(i%12),2)}-${pad(i+1)}`,account:v.account,desc:v.desc,debit:v.amount,credit:v.amount,type:v.type}});
const employeeData = Array.from({length:90},(_,i)=>{const positions=['Branch Manager','Sales Agent','Finance Officer','Collector','Encoder','Inventory Clerk','Branch Cashier','Service Coordinator'];const status=i%23===0?'Absent':i%11===0?'Late':'Present';return {id:`EMP-${pad(i+1)}`,name:personName(390+i),position:positions[i%positions.length],branch:branchData[(i*9)%branchData.length].name,date:'May 12',timeIn:status==='Absent'?'—':i%11===0?'09:08 AM':'08:0'+(i%10)+' AM',timeOut:status==='Absent'||status==='Late'?'—':'05:'+pad(1+(i%49),2)+' PM',hours:status==='Absent'||status==='Late'?'—':(8.5+(i%10)/10).toFixed(2)+' hrs',status}});
const payrollRates={'Branch Manager':48000,'Sales Agent':28000,'Finance Officer':36000,'Collector':26000,'Encoder':23000,'Inventory Clerk':24000,'Branch Cashier':25000,'Service Coordinator':27000};
let payrollData = employeeData.map((e,i)=>{const basic=Math.round((payrollRates[e.position]||25000)/2);const overtime=(i%5)*550;const allowance=1200+(i%4)*350;const deductions=Math.round(basic*.085)+((i%6)*120);const status=i%13===0?'Draft':i%7===0?'Reviewed':i%5===0?'Approved':i%3===0?'Processing':'Released';return {no:`PAY-202605-${pad(i+1)}`,employeeId:e.id,employee:e.name,position:e.position,branch:e.branch,basic,overtime,allowance,deductions,net:basic+overtime+allowance-deductions,status,updated:dateShort(i)}});
const migrationState={
  files:0,
  records:0,
  exports:0,
  sourceType:"inventory",
  activeModule:"dashboard",
  selected:"Inventory CSV",
  imported:[],
  mapping:[
    ['legacy_unit_id','Unit ID','Mapped'],
    ['engine_no','Engine No.','Mapped'],
    ['chassis_serial','Chassis No.','Mapped'],
    ['branch_name','Branch','Mapped'],
    ['retail_price','SRP','Needs Review']
  ],
  logs:[
    ['Migration center opened','Ready to import old database exports'],
    ['Supported sources','CSV, Excel, SQL dump, accounting exports, and legacy reports']
  ]
};
const financingReportData=[
  {unitNo:'UNIT-2026-0104',customerName:'Maria Santos',financingBank:'BDO',orNumber:'OR-2026-01841',crNumber:'CR-2026-01172',salesOrderNo:'SO-2026-0704',invoiceNo:'INV-SO-2026-0504',releaseDate:'May 02, 2026',status:'Ready',remarks:'For bank transmittal'},
  {unitNo:'UNIT-2026-0118',customerName:'Patrick Lim',financingBank:'BPI',orNumber:'OR-2026-01855',crNumber:'CR-2026-01188',salesOrderNo:'SO-2026-0698',invoiceNo:'INV-SO-2026-0510',releaseDate:'May 03, 2026',status:'Transmitted',remarks:'Batch sent to bank'},
  {unitNo:'UNIT-2026-0127',customerName:'Jessa Cruz',financingBank:'Metrobank',orNumber:'OR-2026-01862',crNumber:'CR-2026-01195',salesOrderNo:'SO-2026-0689',invoiceNo:'INV-SO-2026-0518',releaseDate:'May 04, 2026',status:'Ready',remarks:'Complete documents'},
  {unitNo:'UNIT-2026-0142',customerName:'Arnel Dizon',financingBank:'EastWest',orNumber:'OR-2026-01876',crNumber:'CR-2026-01204',salesOrderNo:'SO-2026-0678',invoiceNo:'INV-SO-2026-0529',releaseDate:'May 05, 2026',status:'Pending',remarks:'Awaiting CR copy'},
  {unitNo:'UNIT-2026-0156',customerName:'Liza Ramos',financingBank:'Financing Bank',orNumber:'OR-2026-01889',crNumber:'CR-2026-01218',salesOrderNo:'SO-2026-0669',invoiceNo:'INV-SO-2026-0536',releaseDate:'May 06, 2026',status:'Ready',remarks:'For review'},
  {unitNo:'UNIT-2026-0168',customerName:'Marco Tan',financingBank:'BDO',orNumber:'OR-2026-01901',crNumber:'CR-2026-01231',salesOrderNo:'SO-2026-0661',invoiceNo:'INV-SO-2026-0548',releaseDate:'May 07, 2026',status:'Transmitted',remarks:'Received by branch admin'},
  {unitNo:'UNIT-2026-0175',customerName:'Ana Reyes',financingBank:'BPI',orNumber:'OR-2026-01915',crNumber:'CR-2026-01244',salesOrderNo:'SO-2026-0654',invoiceNo:'INV-SO-2026-0552',releaseDate:'May 08, 2026',status:'Ready',remarks:'Included in next run'},
  {unitNo:'UNIT-2026-0183',customerName:'Camille Go',financingBank:'Metrobank',orNumber:'OR-2026-01924',crNumber:'CR-2026-01259',salesOrderNo:'SO-2026-0648',invoiceNo:'INV-SO-2026-0560',releaseDate:'May 09, 2026',status:'Pending',remarks:'Verify invoice reference'},
  {unitNo:'UNIT-2026-0194',customerName:'Ramon Flores',financingBank:'EastWest',orNumber:'OR-2026-01939',crNumber:'CR-2026-01263',salesOrderNo:'SO-2026-0639',invoiceNo:'INV-SO-2026-0574',releaseDate:'May 10, 2026',status:'Ready',remarks:'Complete documents'},
  {unitNo:'UNIT-2026-0201',customerName:'Nina Villanueva',financingBank:'Financing Bank',orNumber:'OR-2026-01948',crNumber:'CR-2026-01278',salesOrderNo:'SO-2026-0631',invoiceNo:'INV-SO-2026-0581',releaseDate:'May 11, 2026',status:'Transmitted',remarks:'Sent via courier'},
  {unitNo:'UNIT-2026-0212',customerName:'Albert Garcia',financingBank:'BDO',orNumber:'OR-2026-01957',crNumber:'CR-2026-01286',salesOrderNo:'SO-2026-0624',invoiceNo:'INV-SO-2026-0593',releaseDate:'May 12, 2026',status:'Ready',remarks:'Ready for pickup'},
  {unitNo:'UNIT-2026-0225',customerName:'Erika Mendoza',financingBank:'BPI',orNumber:'OR-2026-01970',crNumber:'CR-2026-01299',salesOrderNo:'SO-2026-0618',invoiceNo:'INV-SO-2026-0601',releaseDate:'May 13, 2026',status:'Ready',remarks:'For bank schedule'}
];
let currentOrcrRows=[];
let currentOrcrGenerated='';
const erpMenuModules={
  'payables-menu':{title:'Payables',dataView:'payables',shortcuts:['New Bill','New Payment','Vendor Details','New Vendor'],sections:{Transactions:['Bills and Adjustments','Checks and Payments'],Profiles:['Vendors','Credit Terms'],Processes:['Release AP Documents','Prepare Payments','Process Payments / Print Checks','Release Payments','Generate Intercompany Documents','Close Financial Periods'],Inquiries:['Vendor Details','Vendor Summary'],Reports:['AP Balance by GL Account','AP Balance by Vendor','AP Aging','AP Aged Period Sensitive']}},
  'banking-menu':{title:'Banking',dataView:'banking',shortcuts:['New Cash Entry','New Transfer','New Deposit','Process Bank Records'],sections:{Transactions:['Transactions','Funds Transfers','Reconciliation Statements'],Profiles:['Cash Accounts','Corporate Cards'],Processes:['Import Bank Transactions','Process Bank Transactions','Release Cash Transactions','Close Financial Periods'],Inquiries:['Cash Account Details','Cash Flow Forecast'],Reports:['Cash Account Summary','Cash Account Details','Reconciliation Statement','Cash Requirements']}},
  'gl-menu':{title:'General Ledger',dataView:'gl',shortcuts:['New Journal Entry','Account Summary','Account Details','Reclassify Journal Entries'],sections:{Transactions:['Journal Transactions'],Profiles:['Master Financial Calendar','Allocations','Chart of Accounts','Subaccounts'],Processes:['Run Allocations','Reclassify Transactions','Import Consolidation Data','Manage Financial Periods'],Budgets:['Budgets'],Inquiries:['Account Summary','Account Details'],Reports:['Trial Balance Summary','Transactions for Period','Transactions for Account'],'Financial Statements':['Balance Sheet','Balance Sheet - Comparative','Profit & Loss']}},
  'timeexp-menu':{title:'Time and Expenses',dataView:'timeexp',shortcuts:['New Expense Receipt','New Expense Claim','Employee Time Card'],sections:{Tasks:['Tasks','Approvals','Events'],Email:['Incoming','Draft','Sent'],'Time Tracking':['Weekly Crew Time Entry','Employee Time Activities','Employee Time Cards','Release Time Activities'],'Expense Claims':['Expense Claims','Expense Receipts'],Reports:['Expense Claim Details']}},
  'cashfund-menu':{title:'Cash Fund Management',dataView:'cashfund',sections:{Transactions:['Fund Transaction','Replenishment','Fund Transaction','Replenishment','Cash Advance'],Profiles:['Funds','Funds'],Processes:['Reclassify Cash Advances'],Inquiries:['ATPTEFM - Cash Advance'],'Printed Forms':['Liquidation','Cash Advance Request Form','Fund Transaction Form','Fund Replenishment Form','Request For Payment'],Reports:['Cash Advance Monitoring Report'],Preferences:['Employee Request Class','Cash Advance Preference','Fund Management Preference']}},
  favorites:{title:'Favorites',sections:{'Data Views':['Sold Unit with OR/CR','Reg Tracker Report'],Integration:['Import by Scenario','Data Providers','Import Scenarios'],Services:['CSB OR/CR TRANSMITTAL',{label:'OTHER FINANCING OR/CR TRANSMITTAL',nav:'orcr'},'SUMISHO OR/CR TRANSMITTAL'],Equipment:['Equipment'],Dashboards:['REGISTRATION (NEW)']}},
  'equipment-menu':{title:'Equipment',shortcuts:['New Service Contract','New Equipment'],sections:{Profiles:['Service Contracts','Equipment','Manufacturers','Manufacturer Models'],Processes:['Run Service Contract Billing','Generate from Service Contracts','Process Service Contracts'],Inquiries:['Service Contract Billing Batches','Contract Summary','Contract Schedule Summary','Contract Schedule Detail Summary','Model Equipment and Components'],Reports:['Service Order Details by Contract','Appointment Details by Contract','Service Time Activity by Contract','Appointment Details by Target Equipment'],Preferences:['Equipment Management Preferences']}},
  'inventory-menu':{title:'Inventory',dataView:'inventory',shortcuts:['New Adjustment','New Transfer','New Kit Assembly','New Stock Item'],sections:{Transactions:['Receipts','Issues','Adjustments','Transfers','Kit Assembly'],Profiles:['Stock Items','Item Warehouse Details','Non-Stock Items','Warehouses','Warehouse Buildings'],'Physical Inventory':['Prepare Physical Count','Physical Inventory Count'],Processes:['Release IN Documents','Close Financial Periods'],Inquiries:['Inventory Summary','Storage Summary','Inventory Allocation Details','Inventory Transactions by Account','Inventory Lot/Serial History','Inventory by Item Class','Dead Stock','Intercompany Goods in Transit','Intercompany Returned Goods in Transit'],Reports:['Inventory Balance','Inventory Valuation','Inventory Register','Goods in Transit','Lot/Serial Numbers']}},
  'purchases-menu':{title:'Purchases',dataView:'purchases',shortcuts:['New Purchase Order','New Purchase Receipt','New Purchase Request','New Vendor'],sections:{Transactions:['Requests','Requisitions','Purchase Orders','Purchase Receipts','Landed Costs'],Profiles:['Vendors','Vendor Prices','Vendor Inventory'],Processes:['Create Purchase Orders','Print/Email Purchase Orders','Generate Intercompany Purchase Orders'],'Printed Forms':['Item Request','Purchase Order','Purchase Receipt'],Reports:['Purchase Order Summary','Purchase Order Details by Vendor','Purchase Order Details by Inventory','Blanket Purchase Order Summary','Purchase Order Receipt and Billing','Purchase Receipt Details by Vendor','Purchase Accrual Details','Purchase Receipt Billing Details']}},
  'sales-menu':{title:'Sales Orders',dataView:'sales',shortcuts:['New Sales Order','New Quote','New Payment','New Customer'],sections:{Transactions:[{label:'Sales Orders',nav:'sales'},'Invoices','Shipments'],Profiles:[{label:'Customers',nav:'customers'},'Sales Prices'],Processes:['Process Orders','Generate Intercompany Sales Orders','Process Shipments','Process Invoices and Memos','Create Transfer Orders','Print/Email Orders'],Reports:['Sales Order Summary','Sales Order Details by Customer','Sales Order Details by Inventory','Shipment Summary','Daily Sales Profitability','Sales Profitability by Salesperson','Sales Profitability by Item Class']}},
  'receivables-menu':{title:'Receivables',dataView:'receivables',shortcuts:['New Invoice','New Payment','Customer Details','New Customer'],sections:{Transactions:['Invoices and Memos','Payments and Applications'],Profiles:['Customers','Credit Terms','Sales Prices'],Processes:['Release AR Documents','Print Invoices and Memos','Write Off Balances and Credits','Prepare Statements','Print Statements','Close Financial Periods'],Inquiries:['Customer Details','Customer Summary','Statement History Summary'],'Printed Forms':['Invoice/Memo'],Reports:['AR Balance by GL Account','AR Balance by Customer','AR Aged Past Due','AR Aged Period Sensitive'],'Profitability Analysis':['Sales Profitability Analysis','Daily Sales Profitability','Sales Profitability by Customer and Item','Sales Profitability by Item Class']}}
};
const expenseData = Array.from({length:60},(_,i)=>{const categories=['Transportation','Representation','Fuel','Office Supplies','Branch Repairs','Courier','Meals'];const descriptions=['client visit','bank deposit run','branch delivery vehicle','printer ink and forms','showroom minor repair','document dispatch','field collection route'];return {no:`EXP-202605${pad(12-(i%12),2)}-${pad(i+1)}`,employee:personName(520+i),branch:branchData[(i*4)%branchData.length].name,category:categories[i%categories.length],desc:descriptions[i%descriptions.length],amount:150+((i*431)%8500),date:dateShort(i),status:i%7===0?'Pending':i%5===0?'Reimbursed':'Approved'}});
const cashFundData = Array.from({length:100},(_,i)=>{const branch=branchData[(i*3)%branchData.length],isIn=i%4===0,amount=isIn?10000+((i*137)%45000):750+((i*271)%9800);return {date:dateShort(i),branch:branch.name,ref:`CF-202605${pad(12-(i%12),2)}-${pad(i+1)}`,desc:isIn?'Branch fund replenishment':'Operating cash disbursement',cashIn:isIn?amount:0,cashOut:isIn?0:amount,balance:branch.cashFund,recordedBy:personName(620+i)}});
branchData.forEach(b=>{b.inventory=invData.filter(u=>u.branch===b.name&&u.status!=='Sold').length;b.sold=soData.filter(s=>s.branch===b.name&&s.status==='Completed').length;if(b.inventory<3)b.status='Low Stock'});

// ===== FILTERS =====
let invSort={f:'id',asc:true};
function fmt(n){return new Intl.NumberFormat('en-PH',{minimumFractionDigits:2}).format(n)}
function badge(s){
  const map={Available:'available',Reserved:'reserved',Sold:'sold',Completed:'approved',Processing:'processing',Pending:'pending',Cancelled:'declined',Delivered:'delivered',Approved:'approved',Overdue:'overdue',Paid:'paid','Partial':'partial',Current:'approved',Active:'approved','Low Fund':'reserved','Low Stock':'reserved','Normal':'approved',Present:'approved',Late:'reserved',Absent:'declined',Reimbursed:'paid',Cash:'approved',Credit:'approved',Debit:'declined',Installment:'installment',Draft:'pending',Reviewed:'processing',Released:'paid',Ready:'approved',Transmitted:'paid'};
  return`<span class="badge badge-${map[s]||'pending'}">${s}</span>`;
}

function populateBranchSelects(){
  ['inv-branch','so-branch','ar-branch','cust-branch','te-branch','payroll-branch'].forEach(id=>{
    const el=document.getElementById(id);
    if(!el)return;
    const selected=el.value;
    el.innerHTML='<option value="">All Branches</option>'+branchData.map(b=>`<option>${b.name}</option>`).join('');
    el.value=selected;
  });
}

function renderDashboard(){
  const tb=document.getElementById('dash-so-tb');
  if(!tb)return;
  const invTotal=document.getElementById('dash-inventory-total');
  const soldTotal=document.getElementById('dash-sold-total');
  if(invTotal)invTotal.textContent=invData.length.toLocaleString();
  if(soldTotal)soldTotal.textContent=soData.filter(r=>r.status==='Completed').length.toLocaleString();
  tb.innerHTML=soData.slice(0,12).map(r=>`<tr><td class="mono">${r.no}</td><td>${r.customer}</td><td>${r.model}</td><td class="amt">&#8369;${fmt(r.srp)}</td><td>${badge(r.mode)}</td><td class="dim">${r.branch}</td><td>${badge(r.status)}</td><td class="dim mono">${r.date.replace(', 2026','')}</td></tr>`).join('');
}

function renderCashFund(){
  const grid=document.getElementById('cf-grid');
  const tb=document.getElementById('cf-tb');
  if(grid){
    grid.innerHTML=branchData.map(b=>{
      const pct=Math.min(100,Math.round(b.cashFund/1300));
      const low=b.cashFund<50000;
      return `<div class="cf-card"><div class="cf-branch">${b.name}</div><div class="cf-amount">&#8369;${fmt(b.cashFund).replace('.00','')}</div><div class="cf-meta">Updated: May 12, 2026 &mdash; ${low?'<span style="color:var(--red)">Below threshold</span>':'Normal'}</div><div class="cf-bar"><div class="cf-bar-fill" style="width:${pct}%"></div></div></div>`;
    }).join('');
  }
  if(tb){
    tb.innerHTML=cashFundData.map(r=>`<tr><td class="mono dim">${r.date}</td><td class="dim">${r.branch}</td><td class="mono">${r.ref}</td><td>${r.desc}</td><td class="${r.cashIn?'amt':'dim'}" style="${r.cashIn?'color:var(--green)':''}">${r.cashIn?fmt(r.cashIn):'—'}</td><td class="${r.cashOut?'amt':'dim'}" style="${r.cashOut?'color:var(--red)':''}">${r.cashOut?fmt(r.cashOut):'—'}</td><td class="amt">${fmt(r.balance)}</td><td class="dim">${r.recordedBy}</td></tr>`).join('');
  }
}

function renderEmployees(){
  const emp=document.getElementById('emp-tb');
  const exp=document.getElementById('exp-tb');
  const branch=document.getElementById('te-branch')?.value||'';
  const employees=employeeData.filter(r=>!branch||r.branch===branch);
  const expenses=expenseData.filter(r=>!branch||r.branch===branch);
  if(emp){
    emp.innerHTML=employees.map(r=>`<tr><td class="mono">${r.id}</td><td>${r.name}</td><td class="dim">${r.position}</td><td class="dim">${r.branch}</td><td class="mono dim">${r.date}</td><td class="mono">${r.timeIn}</td><td class="mono">${r.timeOut}</td><td class="mono">${r.hours}</td><td>${badge(r.status)}</td></tr>`).join('');
  }
  if(exp){
    exp.innerHTML=expenses.map(r=>`<tr><td class="mono">${r.no}</td><td>${r.employee}</td><td class="dim">${r.branch}</td><td>${r.category}</td><td>${r.desc}</td><td class="amt">&#8369;${fmt(r.amount)}</td><td class="mono dim">${r.date}</td><td>${badge(r.status)}</td></tr>`).join('');
  }
}

function payrollRows(){
  const q=(document.getElementById('payroll-q')?.value||'').toLowerCase();
  const branch=document.getElementById('payroll-branch')?.value||'';
  const status=document.getElementById('payroll-status')?.value||'';
  return payrollData.filter(r=>(!branch||r.branch===branch)&&(!status||r.status===status)&&(!q||[r.no,r.employee,r.employeeId,r.position,r.branch].join(' ').toLowerCase().includes(q)));
}

function renderPayroll(){
  const tb=document.getElementById('payroll-tb');
  if(!tb)return;
  const rows=payrollRows();
  const gross=rows.reduce((sum,r)=>sum+r.basic+r.overtime+r.allowance,0);
  const net=rows.reduce((sum,r)=>sum+r.net,0);
  const ready=rows.filter(r=>['Approved','Processing','Released'].includes(r.status)).length;
  document.getElementById('payroll-gross').textContent=`₱${fmt(gross).replace('.00','')}`;
  document.getElementById('payroll-net').textContent=`₱${fmt(net).replace('.00','')}`;
  document.getElementById('payroll-ready').textContent=ready;
  document.getElementById('payroll-count').textContent=`${rows.length} of ${payrollData.length} payroll records`;
  document.getElementById('payroll-run-sub').textContent=`${rows.filter(r=>r.status==='Released').length} released, ${rows.filter(r=>r.status==='Draft').length} drafts`;
  document.getElementById('payroll-live').textContent=`Live sync: ${new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true})}`;
  tb.innerHTML=rows.map(r=>`<tr class="row-link" onclick="openPayrollPanel('${r.no}')">
    <td class="mono">${r.no}</td><td><strong>${r.employee}</strong><div class="dim mono" style="font-size:10px">${r.employeeId}</div></td>
    <td class="dim">${r.position}</td><td class="dim">${r.branch}</td>
    <td class="amt">₱${fmt(r.basic)}</td><td class="amt">₱${fmt(r.overtime)}</td><td class="amt">₱${fmt(r.allowance)}</td>
    <td class="amt" style="color:var(--red)">₱${fmt(r.deductions)}</td><td class="amt" style="color:var(--green)">₱${fmt(r.net)}</td>
    <td>${badge(r.status)}</td><td><button class="btn btn-sm" onclick="event.stopPropagation();openPayrollPanel('${r.no}')">View</button></td>
  </tr>`).join('')||'<tr><td colspan="11" style="text-align:center;padding:32px;color:var(--text-tertiary)">No payroll records found</td></tr>';
}
function filterPayroll(){renderPayroll()}

function renderBranches(){
  const tb=document.getElementById('branch-tb');
  if(!tb)return;
  const q=(document.getElementById('branch-q')?.value||'').toLowerCase();
  const region=document.getElementById('branch-region')?.value||'';
  const rows=branchData.filter(r=>(!region||r.region===region)&&(!q||[r.name,r.region,r.address,r.manager].join(' ').toLowerCase().includes(q)));
  tb.innerHTML=rows.map(r=>`<tr><td><strong>${r.name}</strong></td><td class="dim">${r.region}</td><td class="dim">${r.address}</td><td>${r.manager}</td><td class="mono dim">${r.contact}</td><td class="mono">${r.inventory}</td><td class="mono"><strong>${r.sold}</strong></td><td class="amt" style="${r.cashFund<50000?'color:var(--red)':''}">&#8369;${fmt(r.cashFund).replace('.00','')}</td><td>${badge(r.status)}</td></tr>`).join('');
  document.getElementById('branch-count').textContent=`${rows.length} of ${branchData.length} branches`;
}

function renderMigration(){
  const files=document.getElementById('mig-files');
  if(!files)return;
  files.textContent=migrationState.files;
  document.getElementById('mig-records').textContent=migrationState.records;
  const currentType=migrationState.sourceType||migrationTypeForModule(migrationState.activeModule);
  document.getElementById('mig-imported').textContent=migrationState.imported.filter(row=>migrationRecordMatches(row,currentType)).length;
  document.getElementById('mig-exports').textContent=migrationState.exports;
  document.getElementById('migration-mapping').innerHTML=migrationState.mapping.map(row=>`
    <div class="mapping-row">
      <span class="mono">${row[0]}</span>
      <span>${row[1]}</span>
      <span>${badge(row[2])}</span>
    </div>
  `).join('');
  document.getElementById('migration-log').innerHTML=migrationState.logs.slice(-8).reverse().map(row=>`
    <div class="migration-log-item"><strong>${row[0]}</strong><span>${row[1]}</span></div>
  `).join('');
  const preview=document.getElementById('migration-preview');
  if(preview){
    const rows=migrationState.imported.filter(row=>migrationRecordMatches(row,currentType)).slice(0,12);
    preview.innerHTML=rows.length?`
      <div class="table-wrap"><table>
        <thead><tr><th>Module</th><th>Record ID</th><th>Imported Record</th><th>Destination</th><th>Status</th></tr></thead>
        <tbody>${rows.map(row=>`<tr>
          <td>${row.module}</td><td class="mono">${row.id}</td><td><strong>${row.title}</strong></td>
          <td class="dim">${row.destination}</td><td>${badge(row.status)}</td>
        </tr>`).join('')}</tbody>
      </table></div>
      <div class="import-actions">
        <button class="btn btn-sm" onclick="closeMigrationModal();nav('${moduleForMigrationType(currentType)}')">Open ${migrationModuleTitle(currentType)}</button>
      </div>
    `:`<div class="empty-state"><strong>No ${migrationModuleTitle(currentType).toLowerCase()} records imported yet</strong><p>Load a sample file, validate the mapping, then click Import Sample Data. Imported records will appear here and inside this page's live module.</p></div>`;
  }
  renderMigrationContext();
}

function migrationTypeForModule(id){
  const map={dashboard:'inventory',inventory:'inventory',customers:'customers',sales:'sales',receivables:'receivables',purchases:'purchases',payables:'payables',cashfund:'cashfund',banking:'banking',timeexp:'timeexp',payroll:'payroll',branches:'branches',gl:'accounting'};
  return map[id]||'inventory';
}

function exportTypeForModule(id){
  const map={dashboard:'inventory',inventory:'inventory',customers:'customers',sales:'sales',receivables:'receivables',purchases:'purchases',payables:'payables',cashfund:'cashfund',banking:'banking',timeexp:'timeexp',payroll:'payroll',branches:'branches',gl:'accounting'};
  return map[id]||'inventory';
}

function migrationLabel(type){
  const labels={inventory:'Inventory CSV',customers:'Customers XLSX',accounting:'Accounting SQL',sales:'Sales Orders CSV',receivables:'Receivables CSV',purchases:'Purchase Orders CSV',payables:'Payables CSV',cashfund:'Cash Fund CSV',banking:'Banking CSV',timeexp:'Time & Expenses XLSX',payroll:'Payroll CSV',branches:'Branches CSV'};
  return labels[type]||'Inventory CSV';
}

function moduleForMigrationType(type){
  const map={accounting:'gl'};
  return map[type]||type;
}

function migrationModuleTitle(type){
  const moduleId=moduleForMigrationType(type);
  const titles={cashfund:'Cash Fund Management',timeexp:'Time & Expenses'};
  return titles[moduleId]||navMeta[moduleId]?.title||migrationLabel(type).replace(/ (CSV|XLSX|SQL)$/,'');
}

function migrationRecordMatches(row,type){
  return !row.type||row.type===type;
}

function renderMigrationContext(){
  const title=document.getElementById('migration-context-title');
  if(!title)return;
  const importType=migrationState.sourceType||migrationTypeForModule(migrationState.activeModule);
  const exportType=exportTypeForModule(migrationState.activeModule);
  const moduleTitle=migrationModuleTitle(importType);
  document.getElementById('migration-modal-title').textContent=`${moduleTitle} Import / Export`;
  document.getElementById('migration-modal-subtitle').textContent=`This modal belongs to the ${moduleTitle} page only. Imports, exports, previews, and reports stay scoped to this module.`;
  title.textContent=`Current target: ${migrationLabel(importType)}`;
  document.getElementById('migration-context-copy').textContent=`Importing will update ${moduleTitle} records in the live demo, and exporting will download ${migrationLabel(exportType)} from this page context.`;
  document.getElementById('migration-load-context').textContent=`Load ${migrationLabel(importType)} Sample`;
  document.getElementById('migration-export-context').textContent=`Export ${migrationLabel(exportType)}`;
}

function openMigrationModal(sourceType){
  const active=document.querySelector('.module.active')?.id?.replace('mod-','')||'dashboard';
  const type=sourceType||migrationTypeForModule(active);
  const sample=migrationSampleForType(type);
  const isSameContext=migrationState.sourceType===type;
  migrationState.activeModule=moduleForMigrationType(type)||active;
  migrationState.sourceType=type;
  if(!isSameContext){
    migrationState.records=0;
    migrationState.selected=sample.name;
    migrationState.mapping=sample.mapping;
  }
  renderMigrationContext();
  renderMigration();
  openCenterModal('migration-modal-overlay');
}

function closeMigrationModal(){
  closeCenterModal('migration-modal-overlay');
}

function loadContextSample(){
  loadLegacySample(migrationState.sourceType||migrationTypeForModule(migrationState.activeModule));
}

function exportCurrentModule(){
  exportCsv(exportTypeForModule(migrationState.activeModule));
}

function migrationLog(title,detail){
  migrationState.logs.push([title,detail]);
  renderMigration();
}

function previewLegacyFile(input){
  const file=input.files&&input.files[0];
  if(!file)return;
  migrationState.files+=1;
  migrationState.selected=file.name;
  migrationLog('Legacy file selected',`${file.name} queued for field mapping`);
  showToast(`${file.name} ready for validation`);
}

function migrationSampleForType(type){
  const samples={
    inventory:{name:'old_inventory_master.csv',records:1248,mapping:[['unit_code','Unit ID','Mapped'],['motor_model','Model','Mapped'],['engine_number','Engine No.','Mapped'],['dealer_branch','Branch','Mapped'],['unit_srp','SRP','Mapped']]},
    customers:{name:'legacy_customer_accounts.xlsx',records:360,mapping:[['customer_code','Customer ID','Mapped'],['full_name','Full Name','Mapped'],['mobile_no','Contact No.','Mapped'],['home_address','Address','Mapped'],['account_state','Account Status','Needs Review']]},
    accounting:{name:'old_accounting_ledger.sql',records:200,mapping:[['journal_ref','Ref No.','Mapped'],['posting_date','Date','Mapped'],['coa_account','Account','Mapped'],['debit_amount','Debit','Mapped'],['credit_amount','Credit','Mapped']]},
    sales:{name:'old_sales_orders.csv',records:420,mapping:[['sales_ref','SO No.','Mapped'],['customer_name','Customer','Mapped'],['unit_model','Model','Mapped'],['payment_mode','Payment Mode','Mapped'],['order_status','Status','Mapped']]},
    receivables:{name:'old_receivables.csv',records:250,mapping:[['account_ref','Account No.','Mapped'],['customer_name','Customer','Mapped'],['balance_due','Balance','Mapped'],['monthly_due','Monthly Due','Mapped'],['aging_status','Status','Mapped']]},
    purchases:{name:'old_purchase_orders.csv',records:180,mapping:[['po_ref','PO No.','Mapped'],['vendor_name','Supplier','Mapped'],['unit_model','Model','Mapped'],['qty_ordered','Qty','Mapped'],['po_status','Status','Mapped']]},
    payables:{name:'old_supplier_invoices.csv',records:140,mapping:[['invoice_ref','Invoice No.','Mapped'],['vendor_name','Vendor','Mapped'],['invoice_amount','Amount','Mapped'],['amount_paid','Paid','Mapped'],['due_status','Status','Mapped']]},
    cashfund:{name:'old_cash_fund.csv',records:100,mapping:[['cash_ref','Reference','Mapped'],['branch_name','Branch','Mapped'],['cash_in','Cash In','Mapped'],['cash_out','Cash Out','Mapped'],['running_balance','Balance','Mapped']]},
    banking:{name:'old_bank_transactions.csv',records:80,mapping:[['bank_ref','Reference','Mapped'],['bank_name','Bank','Mapped'],['transaction_desc','Description','Mapped'],['debit','Debit','Mapped'],['credit','Credit','Mapped']]},
    timeexp:{name:'old_time_expenses.xlsx',records:120,mapping:[['employee_code','Employee ID','Mapped'],['staff_name','Name','Mapped'],['branch_name','Branch','Mapped'],['hours_worked','Hours','Mapped'],['expense_amount','Amount','Mapped']]},
    payroll:{name:'old_payroll_register.xlsx',records:90,mapping:[['payroll_ref','Payroll No.','Mapped'],['employee_code','Employee ID','Mapped'],['gross_pay','Gross Pay','Mapped'],['deductions','Deductions','Mapped'],['net_pay','Net Pay','Mapped']]},
    branches:{name:'old_branch_directory.csv',records:12,mapping:[['branch_name','Branch Name','Mapped'],['region','Region','Mapped'],['address','Address','Mapped'],['manager_name','Manager','Mapped'],['cash_balance','Cash Fund','Mapped']]}
  };
  return samples[type]||samples.inventory;
}

function isMigrationType(type){
  return ['inventory','customers','accounting','sales','receivables','purchases','payables','cashfund','banking','timeexp','payroll','branches'].includes(type);
}

function loadLegacySample(type){
  const sample=migrationSampleForType(type);
  migrationState.files+=1;
  migrationState.records=sample.records;
  migrationState.sourceType=isMigrationType(type)?type:'inventory';
  migrationState.selected=sample.name;
  migrationState.mapping=sample.mapping;
  migrationLog('Sample legacy file loaded',`${sample.name} with ${sample.records.toLocaleString()} records`);
  showToast(`${sample.name} loaded`);
  renderMigration();
}

function validateLegacyMapping(){
  migrationState.mapping=migrationState.mapping.map(row=>[row[0],row[1],'Mapped']);
  migrationLog('Field mapping validated',`${migrationState.selected} is ready for import`);
  showToast('Legacy fields validated');
  renderMigration();
}

function simulateImport(){
  if(!migrationState.records)loadLegacySample(migrationState.sourceType||migrationTypeForModule(migrationState.activeModule));
  const type=migrationState.sourceType||migrationTypeForModule(migrationState.activeModule);
  let inserted=[];
  if(type==='customers'){
    const base=custData.length+1;
    inserted=[
      {id:`CUS-IMP-${pad(base,4)}`,name:'Legacy Buyer - Maria Santos',contact:'+63 917 442 9012',address:'Quezon City',branch:branchData[1].name,units:1,total:89500,status:'Active'},
      {id:`CUS-IMP-${pad(base+1,4)}`,name:'Legacy Buyer - Patrick Lim',contact:'+63 998 230 1440',address:'Makati City',branch:branchData[2].name,units:2,total:168000,status:'Active'},
      {id:`CUS-IMP-${pad(base+2,4)}`,name:'Legacy Buyer - Jessa Cruz',contact:'+63 906 815 3370',address:'Cebu City',branch:branchData[3].name,units:1,total:74000,status:'Pending'}
    ];
    custData.unshift(...inserted);
    clearImportFilters('customers');
    filterCust();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Customers',id:r.id,title:r.name,destination:'Customer Management',status:r.status})));
    migrationLog('Import completed',`${inserted.length} customer records added to Customer Management`);
    showToast('Imported customers now visible in Customer Management');
  }else if(type==='receivables'){
    const base=arData.length+1;
    inserted=[
      {no:`AR-IMP-${pad(base,4)}`,customer:'Legacy Buyer - Maria Santos',model:'Honda Click 125i',total:72500,balance:58000,monthly:2417,due:'May 30, 2026',overdue:0,branch:branchData[0].name,status:'Current'},
      {no:`AR-IMP-${pad(base+1,4)}`,customer:'Legacy Buyer - Patrick Lim',model:'Yamaha Mio Gear',total:79500,balance:55650,monthly:2319,due:'May 24, 2026',overdue:4,branch:branchData[1].name,status:'Overdue'}
    ];
    arData.unshift(...inserted);
    clearImportFilters('receivables');
    filterAR();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Receivables',id:r.no,title:`${r.customer} - ${r.model}`,destination:'Receivables',status:r.status})));
    migrationLog('Import completed',`${inserted.length} receivable accounts added`);
    showToast('Imported receivables now visible');
  }else if(type==='sales'){
    const base=soData.length+1;
    inserted=[
      {no:`SO-IMP-${pad(base,4)}`,date:'May 14, 2026',customer:'Legacy Buyer - Maria Santos',model:'Honda Click 125i (Black)',srp:72500,dp:14500,mode:'Installment',branch:branchData[0].name,status:'Pending'},
      {no:`SO-IMP-${pad(base+1,4)}`,date:'May 14, 2026',customer:'Legacy Buyer - Patrick Lim',model:'Yamaha Mio Gear (Matte Blue)',srp:79500,dp:23850,mode:'Bank Financing',branch:branchData[1].name,status:'Processing'},
      {no:`SO-IMP-${pad(base+2,4)}`,date:'May 14, 2026',customer:'Legacy Buyer - Jessa Cruz',model:'Suzuki Burgman Street (White)',srp:89500,dp:89500,mode:'Cash',branch:branchData[2].name,status:'Completed'}
    ];
    soData.unshift(...inserted);
    clearImportFilters('sales');
    filterSO();
    renderDashboard();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Sales',id:r.no,title:`${r.customer} - ${r.model}`,destination:'Sales Orders',status:r.status})));
    migrationLog('Import completed',`${inserted.length} sales orders added to Sales Orders`);
    showToast('Imported sales orders now visible in Sales');
  }else if(type==='purchases'){
    const base=poData.length+1;
    inserted=[
      {no:`PO-IMP-${pad(base,4)}`,date:'May 14, 2026',supplier:'Honda Philippines',model:'Honda Click 125i',qty:18,amount:1170000,delivery:'May 22, 2026',branch:branchData[0].name,status:'Pending'},
      {no:`PO-IMP-${pad(base+1,4)}`,date:'May 14, 2026',supplier:'Yamaha Motor PH',model:'Yamaha Mio Gear',qty:12,amount:882000,delivery:'May 24, 2026',branch:branchData[1].name,status:'Approved'}
    ];
    poData.unshift(...inserted);
    clearImportFilters('purchases');
    filterPO();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Purchases',id:r.no,title:`${r.supplier} - ${r.model}`,destination:'Purchase Orders',status:r.status})));
    migrationLog('Import completed',`${inserted.length} purchase orders added`);
    showToast('Imported purchase orders now visible');
  }else if(type==='payables'){
    const base=apData.length+1;
    inserted=[
      {inv:`INV-IMP-${pad(base,4)}`,vendor:'Honda Philippines',desc:'Legacy supplier invoice import',amount:1170000,paid:450000,due:720000,invDate:'May 14, 2026',dueDate:'May 30, 2026',status:'Partial'},
      {inv:`INV-IMP-${pad(base+1,4)}`,vendor:'Yamaha Motor PH',desc:'Legacy supplier invoice import',amount:882000,paid:0,due:882000,invDate:'May 14, 2026',dueDate:'May 28, 2026',status:'Pending'}
    ];
    apData.unshift(...inserted);
    clearImportFilters('payables');
    filterAP();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Payables',id:r.inv,title:`${r.vendor} - ${r.desc}`,destination:'Payables',status:r.status})));
    migrationLog('Import completed',`${inserted.length} supplier invoices added`);
    showToast('Imported payables now visible');
  }else if(type==='cashfund'){
    inserted=[
      {date:'05/14/26',branch:branchData[0].name,ref:`CF-IMP-${pad(cashFundData.length+1,4)}`,desc:'Legacy cash fund replenishment import',cashIn:25000,cashOut:0,balance:branchData[0].cashFund+25000,recordedBy:'Legacy Import'},
      {date:'05/14/26',branch:branchData[1].name,ref:`CF-IMP-${pad(cashFundData.length+2,4)}`,desc:'Legacy operating cash disbursement import',cashIn:0,cashOut:8500,balance:branchData[1].cashFund-8500,recordedBy:'Legacy Import'}
    ];
    cashFundData.unshift(...inserted);
    renderCashFund();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Cash Fund',id:r.ref,title:`${r.branch} - ${r.desc}`,destination:'Cash Fund Management',status:r.cashIn?'Credit':'Debit'})));
    migrationLog('Import completed',`${inserted.length} cash fund entries added`);
    showToast('Imported cash fund entries now visible');
  }else if(type==='banking'){
    inserted=[
      {date:'May 14',bank:'BPI',ref:`BPI-IMP-${pad(migrationState.imported.length+1,4)}`,desc:'Legacy bank collection import',debit:0,credit:96500,balance:12577000,type:'Credit'},
      {date:'May 14',bank:'BDO',ref:`BDO-IMP-${pad(migrationState.imported.length+2,4)}`,desc:'Legacy supplier payment import',debit:42000,credit:0,balance:6859200,type:'Debit'}
    ];
    const bankTb=document.getElementById('bank-tb');
    if(bankTb)bankTb.insertAdjacentHTML('afterbegin',inserted.map(r=>`<tr><td class="mono dim">${r.date}</td><td>${r.bank}</td><td class="mono">${r.ref}</td><td>${r.desc}</td><td class="${r.debit?'amt':'dim'}" style="${r.debit?'color:var(--red)':''}">${r.debit?fmt(r.debit):'—'}</td><td class="${r.credit?'amt':'dim'}" style="${r.credit?'color:var(--green)':''}">${r.credit?fmt(r.credit):'—'}</td><td class="amt">${fmt(r.balance)}</td><td>${badge(r.type)}</td></tr>`).join(''));
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Banking',id:r.ref,title:`${r.bank} - ${r.desc}`,destination:'Banking',status:r.type})));
    migrationLog('Import completed',`${inserted.length} bank transactions added`);
    showToast('Imported bank transactions now visible');
  }else if(type==='timeexp'){
    inserted=[
      {id:`EMP-IMP-${pad(employeeData.length+1,3)}`,name:'Legacy Staff - Arnel Cruz',position:'Sales Agent',branch:branchData[0].name,date:'May 14',timeIn:'08:05 AM',timeOut:'05:10 PM',hours:'9.08 hrs',status:'Present'},
      {id:`EMP-IMP-${pad(employeeData.length+2,3)}`,name:'Legacy Staff - Liza Ramos',position:'Encoder',branch:branchData[1].name,date:'May 14',timeIn:'09:12 AM',timeOut:'—',hours:'—',status:'Late'}
    ];
    employeeData.unshift(...inserted);
    renderEmployees();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Time & Expenses',id:r.id,title:`${r.name} - ${r.position}`,destination:'Time & Expenses',status:r.status})));
    migrationLog('Import completed',`${inserted.length} attendance records added`);
    showToast('Imported attendance records now visible');
  }else if(type==='payroll'){
    const base=payrollData.length+1;
    inserted=[
      {no:`PAY-IMP-${pad(base,4)}`,employeeId:'EMP-LEG-101',employee:'Legacy Employee - Ana Reyes',position:'Branch Cashier',branch:branchData[0].name,basic:14500,overtime:1100,allowance:1800,deductions:1520,net:15880,status:'Reviewed',updated:'05/14/26'},
      {no:`PAY-IMP-${pad(base+1,4)}`,employeeId:'EMP-LEG-102',employee:'Legacy Employee - Marco Tan',position:'Sales Agent',branch:branchData[1].name,basic:14000,overtime:2200,allowance:1550,deductions:1390,net:16360,status:'Approved',updated:'05/14/26'},
      {no:`PAY-IMP-${pad(base+2,4)}`,employeeId:'EMP-LEG-103',employee:'Legacy Employee - Camille Go',position:'Finance Officer',branch:branchData[2].name,basic:18000,overtime:550,allowance:1900,deductions:1810,net:18640,status:'Processing',updated:'05/14/26'}
    ];
    payrollData.unshift(...inserted);
    clearImportFilters('payroll');
    filterPayroll();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Payroll',id:r.no,title:`${r.employee} - ${r.position}`,destination:'Payroll Management',status:r.status})));
    migrationLog('Import completed',`${inserted.length} payroll records added to Payroll Management`);
    showToast('Imported payroll records now visible in Payroll');
  }else if(type==='accounting'){
    const base=glData.length+1;
    inserted=[
      {date:'05/14/26',ref:`JE-IMP-${pad(base,4)}`,account:'Cash in Bank / Sales Revenue',desc:'Legacy sales deposit imported from old ledger',debit:145000,credit:145000,type:'Sales'},
      {date:'05/14/26',ref:`JE-IMP-${pad(base+1,4)}`,account:'Accounts Receivable / Sales Revenue',desc:'Legacy installment account balance imported',debit:92500,credit:92500,type:'Payment'},
      {date:'05/14/26',ref:`JE-IMP-${pad(base+2,4)}`,account:'Operating Expense / Cash Fund',desc:'Legacy branch expense imported',debit:18450,credit:18450,type:'Adjustment'}
    ];
    glData.unshift(...inserted);
    clearImportFilters('accounting');
    filterGL();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Accounting',id:r.ref,title:r.desc,destination:'General Ledger',status:r.type})));
    migrationLog('Import completed',`${inserted.length} ledger entries added to Accounting / GL`);
    showToast('Imported ledger entries now visible in Accounting');
  }else if(type==='branches'){
    inserted=[
      {name:'Imported Legacy Branch',region:'Luzon',address:'Legacy Avenue, Batangas City',manager:'Legacy Manager',contact:'+63 917 000 2026',cashFund:78000,status:'Normal'}
    ];
    branchData.unshift(...inserted);
    refreshBranchInventory();
    populateBranchSelects();
    renderBranches();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Branches',id:r.name,title:`${r.region} - ${r.address}`,destination:'Branch Directory',status:r.status})));
    migrationLog('Import completed',`${inserted.length} branch record added`);
    showToast('Imported branch now visible');
  }else{
    const base=invData.length+1;
    inserted=[
      {id:`UNIT-IMP-${pad(base,4)}`,brand:'Honda',model:'Legacy Click 125i',color:'Black',engine:`ENG-OLD-${pad(7000+migrationState.files,5)}`,chassis:`CHS-OLD-${pad(8000+migrationState.files,5)}`,branch:branchData[0].name,status:'Available',price:72500,date:'May 14, 2026'},
      {id:`UNIT-IMP-${pad(base+1,4)}`,brand:'Yamaha',model:'Legacy Mio Gear',color:'Matte Blue',engine:`ENG-OLD-${pad(7010+migrationState.files,5)}`,chassis:`CHS-OLD-${pad(8010+migrationState.files,5)}`,branch:branchData[1].name,status:'Reserved',price:79500,date:'May 14, 2026'},
      {id:`UNIT-IMP-${pad(base+2,4)}`,brand:'Suzuki',model:'Legacy Burgman Street',color:'White',engine:`ENG-OLD-${pad(7020+migrationState.files,5)}`,chassis:`CHS-OLD-${pad(8020+migrationState.files,5)}`,branch:branchData[2].name,status:'Available',price:89500,date:'May 14, 2026'}
    ];
    invData.unshift(...inserted);
    refreshBranchInventory();
    clearImportFilters('inventory');
    filterInv();
    renderDashboard();
    renderBranches();
    migrationState.imported.unshift(...inserted.map(r=>({type,module:'Inventory',id:r.id,title:`${r.brand} ${r.model}`,destination:'Inventory Management',status:r.status})));
    migrationLog('Import completed',`${inserted.length} motorcycle units added to Inventory Management`);
    showToast('Imported units now visible in Inventory Management');
  }
  migrationState.records=Math.max(migrationState.records,inserted.length);
  renderMigration();
}

function clearImportFilters(type){
  const ids=type==='customers'?['cust-q','cust-branch']:type==='accounting'?['gl-q','gl-type']:type==='sales'?['so-q','so-status','so-mode','so-branch']:type==='receivables'?['ar-q','ar-status','ar-branch']:type==='purchases'?['po-q','po-status','po-supplier']:type==='payables'?['ap-q','ap-status']:type==='payroll'?['payroll-q','payroll-branch','payroll-status']:['inv-q','inv-brand','inv-status','inv-branch'];
  ids.forEach(id=>{const el=document.getElementById(id);if(el)el.value=''});
}

function refreshBranchInventory(){
  branchData.forEach(b=>{
    b.inventory=invData.filter(u=>u.branch===b.name&&u.status!=='Sold').length;
    b.sold=soData.filter(s=>s.branch===b.name&&s.status==='Completed').length;
    b.status=b.inventory<3?'Low Stock':'Normal';
  });
}

function csvEscape(value){
  const text=String(value??'');
  return /[",\n]/.test(text)?`"${text.replace(/"/g,'""')}"`:text;
}

function toCsv(rows,headers){
  return [headers.join(','),...rows.map(row=>headers.map(key=>csvEscape(row[key])).join(','))].join('\n');
}

function downloadFile(filename,content,type='text/plain;charset=utf-8'){
  const blob=new Blob([content],{type});
  const url=URL.createObjectURL(blob);
  const a=document.createElement('a');
  a.href=url;
  a.download=filename;
  a.click();
  URL.revokeObjectURL(url);
}

function exportCsv(type){
  const sources={
    inventory:{name:'inventory-export.csv',rows:invData.slice(0,120),headers:['id','brand','model','engine','chassis','color','branch','status','price','date']},
    customers:{name:'customers-export.csv',rows:custData.slice(0,120),headers:['id','name','contact','address','branch','units','total','status']},
    sales:{name:'sales-orders-export.csv',rows:soData.slice(0,120),headers:['no','date','customer','model','srp','dp','mode','branch','status']},
    receivables:{name:'receivables-export.csv',rows:arData.slice(0,120),headers:['no','customer','model','total','balance','monthly','due','overdue','branch','status']},
    purchases:{name:'purchase-orders-export.csv',rows:poData.slice(0,120),headers:['no','date','supplier','model','qty','amount','delivery','branch','status']},
    payables:{name:'payables-export.csv',rows:apData.slice(0,120),headers:['inv','vendor','desc','amount','paid','due','invDate','dueDate','status']},
    cashfund:{name:'cash-fund-export.csv',rows:cashFundData.slice(0,120),headers:['date','branch','ref','desc','cashIn','cashOut','balance','recordedBy']},
    accounting:{name:'general-ledger-export.csv',rows:glData.slice(0,120),headers:['date','ref','account','desc','debit','credit','type']},
    timeexp:{name:'time-expenses-export.csv',rows:employeeData.slice(0,120),headers:['id','name','position','branch','date','timeIn','timeOut','hours','status']},
    payroll:{name:'payroll-export.csv',rows:payrollData.slice(0,120),headers:['no','employeeId','employee','position','branch','basic','overtime','allowance','deductions','net','status']},
    branches:{name:'branches-export.csv',rows:branchData.slice(0,120),headers:['name','region','address','manager','contact','inventory','sold','cashFund','status']},
    banking:{name:'banking-export.csv',rows:[...document.querySelectorAll('#bank-tb tr')].slice(0,120).map(row=>{const c=[...row.children].map(td=>td.textContent.trim());return {date:c[0],bank:c[1],ref:c[2],desc:c[3],debit:c[4],credit:c[5],balance:c[6],type:c[7]}}),headers:['date','bank','ref','desc','debit','credit','balance','type']}
  };
  const source=sources[type]||sources.inventory;
  downloadFile(source.name,toCsv(source.rows,source.headers),'text/csv;charset=utf-8');
  migrationState.exports+=1;
  migrationLog('Export generated',`${source.name} downloaded with ${source.rows.length} sample records`);
  renderMigration();
  showToast(`${source.name} exported`);
}

function exportMigrationReport(){
  const currentType=migrationState.sourceType||migrationTypeForModule(migrationState.activeModule);
  const moduleTitle=migrationModuleTitle(currentType);
  const importedRows=migrationState.imported.filter(row=>migrationRecordMatches(row,currentType));
  const content=[
    `NEXII BSM ${moduleTitle.toUpperCase()} IMPORT / EXPORT REPORT`,
    `Generated: ${new Date().toLocaleString('en-PH')}`,
    '',
    `Imported files: ${migrationState.files}`,
    `Validated records: ${migrationState.records}`,
    `Export packages: ${migrationState.exports}`,
    `Current source: ${migrationState.selected}`,
    `Current page: ${moduleTitle}`,
    `Imported to this page: ${importedRows.length}`,
    '',
    'Field mapping:',
    ...migrationState.mapping.map(row=>`- ${row[0]} -> ${row[1]} (${row[2]})`),
    '',
    'Imported records:',
    ...(importedRows.length?importedRows.map(row=>`- ${row.module}: ${row.id} - ${row.title} -> ${row.destination} (${row.status})`):['- None yet']),
    '',
    'Activity:',
    ...migrationState.logs.slice(-12).map(row=>`- ${row[0]}: ${row[1]}`)
  ].join('\n');
  downloadFile(`nexii-bsm-${currentType}-import-export-report.txt`,content);
  migrationState.exports+=1;
  migrationLog('Migration report exported','Client-ready import/export report downloaded');
  renderMigration();
  showToast('Migration report exported');
}

function initializeDemo(){
  renderErpMenuPages();
  addMenuViewButtons();
  populateBranchSelects();
  renderDashboard();
  renderCashFund();
  renderEmployees();
  renderPayroll();
  renderBranches();
  renderMigration();
}

function filterInv(){
  const q=document.getElementById('inv-q').value.toLowerCase();
  const br=document.getElementById('inv-brand').value;
  const st=document.getElementById('inv-status').value;
  const bch=document.getElementById('inv-branch').value;
  let d=invData.filter(r=>
    (!br||r.brand===br)&&(!st||r.status===st)&&(!bch||r.branch===bch)&&
    (!q||[r.id,r.model,r.engine,r.chassis,r.color,r.branch].join(' ').toLowerCase().includes(q))
  );
  d.sort((a,b)=>{const f=invSort.f;const v=invSort.asc?1:-1;return typeof a[f]==='number'?v*(a[f]-b[f]):v*String(a[f]).localeCompare(String(b[f]))});
  document.getElementById('inv-count').textContent=`${d.length} of ${invData.length} units`;
  document.getElementById('inv-ft').textContent=`${d.length} units`;
  document.getElementById('inv-tb').innerHTML=d.map(r=>`<tr class="row-link" onclick="openInvPanel(${JSON.stringify(r).replace(/"/g,'&quot;')})">
    <td class="mono">${r.id}</td><td>${r.brand}</td><td><strong>${r.model}</strong></td>
    <td class="mono">${r.engine}</td><td class="mono">${r.chassis}</td>
    <td class="dim">${r.color}</td><td class="dim">${r.branch}</td><td>${badge(r.status)}</td>
    <td class="amt">&#8369;${fmt(r.price)}</td><td class="mono dim">${r.date}</td>
  </tr>`).join('')||'<tr><td colspan="10" style="text-align:center;padding:32px;color:var(--text-tertiary)">No units found</td></tr>';
}
function srtInv(f){invSort=invSort.f===f?{f,asc:!invSort.asc}:{f,asc:true};filterInv()}

function filterSO(){
  const q=document.getElementById('so-q').value.toLowerCase();
  const st=document.getElementById('so-status').value;
  const m=document.getElementById('so-mode').value;
  const b=document.getElementById('so-branch').value;
  let d=soData.filter(r=>(!st||r.status===st)&&(!m||r.mode===m)&&(!b||r.branch===b)&&(!q||[r.no,r.customer,r.model].join(' ').toLowerCase().includes(q)));
  document.getElementById('so-count').textContent=`${d.length} orders`;
  document.getElementById('so-ft').textContent=`${d.length} of ${soData.length} orders`;
  document.getElementById('so-tb').innerHTML=d.map(r=>`<tr class="row-link" onclick="openSOPanel(${JSON.stringify(r).replace(/"/g,'&quot;')})">
    <td class="mono">${r.no}</td><td class="mono dim">${r.date.replace(', 2026','')}</td>
    <td><strong>${r.customer}</strong></td><td class="dim">${r.model}</td>
    <td class="amt">&#8369;${fmt(r.srp)}</td><td class="amt">&#8369;${fmt(r.dp)}</td>
    <td>${badge(r.mode==='Installment'?'Installment':r.mode==='Bank Financing'?'Processing':'Approved')}</td>
    <td class="dim">${r.branch}</td><td>${badge(r.status)}</td>
  </tr>`).join('')||'<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-tertiary)">No orders found</td></tr>';
}

function filterAR(){
  const q=document.getElementById('ar-q').value.toLowerCase();
  const st=document.getElementById('ar-status').value;
  const b=document.getElementById('ar-branch').value;
  let d=arData.filter(r=>(!st||r.status===st)&&(!b||r.branch===b)&&(!q||[r.no,r.customer,r.model].join(' ').toLowerCase().includes(q)));
  document.getElementById('ar-count').textContent=`${d.length} accounts`;
  document.getElementById('ar-ft').textContent=`${d.length} of ${arData.length} accounts`;
  document.getElementById('ar-tb').innerHTML=d.map(r=>`<tr class="row-link">
    <td class="mono">${r.no}</td><td><strong>${r.customer}</strong></td><td class="dim">${r.model}</td>
    <td class="amt">&#8369;${fmt(r.total)}</td><td class="amt" style="${r.status==='Overdue'?'color:var(--red)':''}">&#8369;${fmt(r.balance)}</td>
    <td class="amt">${r.monthly?'&#8369;'+fmt(r.monthly):'—'}</td>
    <td class="mono dim">${r.due}</td>
    <td class="mono${r.overdue>0?' ':"dim"}" style="${r.overdue>0?'color:var(--red)':''}">${r.overdue>0?r.overdue+'d':'—'}</td>
    <td class="dim">${r.branch}</td><td>${badge(r.status)}</td>
  </tr>`).join('')||'<tr><td colspan="10" style="text-align:center;padding:32px;color:var(--text-tertiary)">No accounts found</td></tr>';
}

function filterCust(){
  const q=document.getElementById('cust-q').value.toLowerCase();
  const b=document.getElementById('cust-branch').value;
  let d=custData.filter(r=>(!b||r.branch===b)&&(!q||[r.id,r.name,r.contact,r.address].join(' ').toLowerCase().includes(q)));
  document.getElementById('cust-count').textContent=`${d.length} customers`;
  document.getElementById('cust-ft').textContent=`${d.length} of ${custData.length} customers`;
  document.getElementById('cust-tb').innerHTML=d.map(r=>`<tr class="row-link" style="cursor:pointer" onclick="openCustomerProfile('${r.id}')">
    <td class="mono">${r.id}</td><td><strong>${r.name}</strong></td>
    <td class="mono dim">${r.contact}</td><td class="dim">${r.address}</td>
    <td class="dim">${r.branch}</td><td class="mono" style="text-align:center">${r.units}</td>
    <td class="amt">&#8369;${fmt(r.total)}</td><td>${badge(r.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" onclick="event.stopPropagation(); openCustomerProfile('${r.id}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
  </tr>`).join('');
}

// --- NEW CUSTOMER PROFILE LOGIC ---
function initCustomerProfileData(customerId) {
  let stored = JSON.parse(localStorage.getItem('customerProfileDemoData')) || {};
  if (stored[customerId]) return stored[customerId];

  const cust = custData.find(c => c.id === customerId);
  if (!cust) return null;

  // Generate orders based on units purchased
  const orders = [];
  let totalPurchased = 0;
  for (let i = 0; i < cust.units; i++) {
    const unit = catalog[Math.floor(Math.random() * catalog.length)];
    const qty = 1;
    const price = unit.price;
    const amount = qty * price;
    totalPurchased += amount;
    const isPaid = cust.status === 'Active' && Math.random() > 0.5;
    const status = isPaid ? 'Completed' : (Math.random() > 0.5 ? 'Processing' : 'Pending');
    const mode = ['Cash', 'GCash', 'Bank Transfer', 'Check', 'Financing'][Math.floor(Math.random() * 5)];
    orders.push({
      no: `ORD-2026-${pad(Math.floor(Math.random() * 9999), 4)}`,
      date: dateLong(Math.floor(Math.random() * 30)),
      model: `${unit.brand} ${unit.model}`,
      qty,
      price,
      amount,
      mode,
      status
    });
  }

  let totalPaid = 0;
  let remainingBalance = 0;
  let nextDueDate = '—';
  let monthlyDue = 0;
  let schedule = [];
  let payments = [];

  if (cust.status === 'Overdue') {
    remainingBalance = totalPurchased * 0.4;
    totalPaid = totalPurchased - remainingBalance;
    nextDueDate = isoDate(1); // past date
    monthlyDue = Math.round(remainingBalance / 6);
  } else if (cust.status === 'Active') {
    remainingBalance = Math.random() > 0.5 ? totalPurchased * 0.2 : 0;
    totalPaid = totalPurchased - remainingBalance;
    if (remainingBalance > 0) {
      nextDueDate = isoDate(15); // future date
      monthlyDue = Math.round(remainingBalance / 6);
    }
  }

  // Generate some payments if totalPaid > 0
  if (totalPaid > 0) {
    payments.push({
      no: `PAY-2026-${pad(Math.floor(Math.random() * 9999), 4)}`,
      date: dateLong(Math.floor(Math.random() * 10)),
      method: ['Cash', 'GCash', 'Bank Transfer'][Math.floor(Math.random() * 3)],
      ref: `REF-${pad(Math.floor(Math.random() * 99999), 5)}`,
      amount: totalPaid,
      appliedTo: orders.length > 0 ? orders[0].no : 'Balance',
      status: 'Applied'
    });
  }

  // Generate payment schedule
  if (remainingBalance > 0) {
    schedule.push({
      dueDate: nextDueDate,
      amountDue: monthlyDue,
      amountPaid: 0,
      balance: monthlyDue,
      status: cust.status === 'Overdue' ? 'Overdue' : 'Due Soon'
    });
  }

  const profile = {
    customerId: cust.id,
    fullName: cust.name,
    contact: cust.contact,
    address: cust.address,
    branch: cust.branch,
    accountStatus: cust.status,
    orders,
    totalPurchased,
    totalPaid,
    remainingBalance,
    nextDueDate,
    monthlyDue,
    paymentSchedule: schedule,
    payments
  };

  stored[customerId] = profile;
  localStorage.setItem('customerProfileDemoData', JSON.stringify(stored));
  return profile;
}

function getDueStatus(dueDate, balance) {
  if (balance <= 0) return 'Paid';
  if (dueDate === '—') return 'Pending';
  const today = new Date('2026-05-15');
  const due = new Date(dueDate);
  const diffDays = Math.ceil((due - today) / (1000 * 60 * 60 * 24));
  if (diffDays < 0) return Math.abs(diffDays) + ' Days Overdue';
  if (diffDays === 0) return 'Due Today';
  return 'Due in ' + diffDays + ' Days';
}

function openCustomerProfile(customerId) {
  const profile = initCustomerProfileData(customerId);
  if (!profile) return;
  const html = renderCustomerProfile(profile);
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="openRecordCustomerPayment('${profile.customerId}')">Record Demo Payment</button>
    <button class="btn btn-sm" onclick="showToast('Demo customer profile prepared for printing.'); setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm" onclick="exportCustomerProfileCsv('${profile.customerId}')">Export Customer CSV</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Customer Profile: ' + profile.fullName, html, actions, { width: 'min(980px, calc(100vw - 48px))' });
}

function renderCustomerProfile(profile) {
  const dueStatus = getDueStatus(profile.nextDueDate, profile.remainingBalance);
  const isOverdue = dueStatus.includes('Overdue');

  const ordersHtml = profile.orders.length ? profile.orders.map(o => `<tr>
    <td class="mono">${o.no}</td><td class="dim">${o.date}</td><td>${o.model}</td><td class="mono" style="text-align:center">${o.qty}</td>
    <td class="amt">&#8369;${fmt(o.price)}</td><td class="amt">&#8369;${fmt(o.amount)}</td><td>${o.mode}</td><td>${badge(o.status)}</td>
  </tr>`).join('') : '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No orders found</td></tr>';

  const scheduleHtml = profile.paymentSchedule.length ? profile.paymentSchedule.map(s => `<tr>
    <td class="mono">${s.dueDate}</td><td class="amt">&#8369;${fmt(s.amountDue)}</td><td class="amt" style="color:var(--green)">&#8369;${fmt(s.amountPaid)}</td>
    <td class="amt" style="${s.balance > 0 && s.status === 'Overdue' ? 'color:var(--red)' : ''}">&#8369;${fmt(s.balance)}</td><td>${badge(s.status)}</td>
  </tr>`).join('') : '<tr><td colspan="5" style="text-align:center;color:var(--text-tertiary)">No schedule</td></tr>';

  const historyHtml = profile.payments.length ? profile.payments.map(p => `<tr>
    <td class="mono">${p.no}</td><td class="dim">${p.date}</td><td>${p.method}</td><td class="mono dim">${p.ref}</td>
    <td class="amt" style="color:var(--green)">&#8369;${fmt(p.amount)}</td><td class="mono">${p.appliedTo}</td><td>${badge(p.status)}</td>
  </tr>`).join('') : '<tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No payment history</td></tr>';

  return `
    <div class="sp-section">
      <div class="sp-section-label">Basic Information</div>
      <div class="sp-row"><span class="sp-key">Customer ID</span><span class="sp-val mono">${profile.customerId}</span></div>
      <div class="sp-row"><span class="sp-key">Full Name</span><span class="sp-val">${profile.fullName}</span></div>
      <div class="sp-row"><span class="sp-key">Contact Number</span><span class="sp-val mono">${profile.contact}</span></div>
      <div class="sp-row"><span class="sp-key">Address</span><span class="sp-val">${profile.address}</span></div>
      <div class="sp-row"><span class="sp-key">Branch</span><span class="sp-val">${profile.branch}</span></div>
      <div class="sp-row"><span class="sp-key">Account Status</span><span class="sp-val">${badge(profile.accountStatus)}</span></div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Purchase / Order Summary</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Order No.</th><th>Date</th><th>Unit Model</th><th style="text-align:center">Qty</th><th>Unit Price</th><th>Total Amount</th><th>Payment Mode</th><th>Status</th></tr></thead>
          <tbody>${ordersHtml}</tbody>
        </table>
      </div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Balance Summary</div>
      <div class="sp-row"><span class="sp-key">Total Purchased Amount</span><span class="sp-val mono">&#8369;${fmt(profile.totalPurchased)}</span></div>
      <div class="sp-row"><span class="sp-key">Total Paid</span><span class="sp-val mono" style="color:var(--green)">&#8369;${fmt(profile.totalPaid)}</span></div>
      <div class="sp-row"><span class="sp-key">Remaining Balance</span><span class="sp-val mono" style="${profile.remainingBalance>0?'color:var(--red)':''}">&#8369;${fmt(profile.remainingBalance)}</span></div>
      <div class="sp-row"><span class="sp-key">Next Due Date</span><span class="sp-val mono">${profile.nextDueDate}</span></div>
      <div class="sp-row"><span class="sp-key">Monthly Due</span><span class="sp-val mono">&#8369;${fmt(profile.monthlyDue)}</span></div>
      <div class="sp-row"><span class="sp-key">Payment Status</span><span class="sp-val" style="${isOverdue?'color:var(--red);font-weight:600':''}">${dueStatus}</span></div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Payment Schedule</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Due Date</th><th>Amount Due</th><th>Amount Paid</th><th>Balance</th><th>Status</th></tr></thead>
          <tbody>${scheduleHtml}</tbody>
        </table>
      </div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Payment History</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Payment No.</th><th>Date</th><th>Method</th><th>Reference No.</th><th>Amount Paid</th><th>Applied To</th><th>Status</th></tr></thead>
          <tbody>${historyHtml}</tbody>
        </table>
      </div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Activity Log</div>
      <div style="border-left:2px solid var(--accent);padding-left:14px;margin-top:6px">
        <div style="display:flex;flex-direction:column;gap:8px">
          <div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--accent);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Customer profile created</span></div>
          </div>
          ${profile.orders.length ? `<div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--green);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Sales order created</span></div>
          </div>` : ''}
          ${profile.payments.length ? `<div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--green);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Payment recorded</span></div>
          </div>` : ''}
          ${profile.remainingBalance > 0 ? `<div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--text-tertiary);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Statement generated</span></div>
          </div>` : ''}
        </div>
      </div>
    </div>
  `;
}

function openRecordCustomerPayment(customerId) {
  let stored = JSON.parse(localStorage.getItem('customerProfileDemoData')) || {};
  const profile = stored[customerId];
  if (!profile) return;

  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Payment Date</label><input type="date" id="rcp-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Payment Method</label><select id="rcp-method"><option>Cash</option><option>GCash</option><option>Bank Transfer</option><option>Check</option></select></div>
      <div class="demo-form-row"><label>Reference Number</label><input type="text" id="rcp-ref" placeholder="e.g. TXN-12345"></div>
      <div class="demo-form-row"><label>Amount Paid (&#8369;)</label><input type="number" id="rcp-amount" placeholder="0.00" value="${profile.remainingBalance > 0 ? profile.monthlyDue || profile.remainingBalance : ''}"></div>
      <div class="demo-form-row"><label>Apply To</label><select id="rcp-apply"><option>Balance</option>${profile.orders.map(o=>`<option value="${o.no}">${o.no}</option>`).join('')}</select></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="saveCustomerDemoPayment('${customerId}', this)">Save Payment</button>
    <button class="btn btn-sm" onclick="openCustomerProfile('${customerId}')">Cancel</button>
  `;
  openCenterModal('Record Payment: ' + profile.fullName, html, actions);
}

function saveCustomerDemoPayment(customerId, btn) {
  const amtInput = document.getElementById('rcp-amount');
  const amt = parseFloat(amtInput.value);
  if (isNaN(amt) || amt <= 0) { showToast('Please enter a valid amount'); return; }

  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    let stored = JSON.parse(localStorage.getItem('customerProfileDemoData')) || {};
    const profile = stored[customerId];
    if (profile) {
      profile.totalPaid += amt;
      profile.remainingBalance = Math.max(0, profile.remainingBalance - amt);
      
      profile.payments.unshift({
        no: `PAY-2026-${pad(profile.payments.length + 8000, 4)}`,
        date: document.getElementById('rcp-date').value,
        method: document.getElementById('rcp-method').value,
        ref: document.getElementById('rcp-ref').value || 'N/A',
        amount: amt,
        appliedTo: document.getElementById('rcp-apply').value,
        status: 'Applied'
      });

      if (profile.remainingBalance === 0) {
        profile.accountStatus = 'Paid';
        profile.nextDueDate = '—';
        profile.monthlyDue = 0;
        profile.paymentSchedule.forEach(s => {
          if(s.balance > 0) {
            s.amountPaid += s.balance;
            s.balance = 0;
            s.status = 'Paid';
          }
        });
      } else if (profile.paymentSchedule.length > 0) {
         let sched = profile.paymentSchedule[0];
         sched.amountPaid += amt;
         sched.balance = Math.max(0, sched.amountDue - sched.amountPaid);
         if (sched.balance === 0) sched.status = 'Paid';
      }

      stored[customerId] = profile;
      localStorage.setItem('customerProfileDemoData', JSON.stringify(stored));
      
      // Update the main custData to reflect changes in UI
      const cust = custData.find(c => c.id === customerId);
      if(cust) {
          cust.status = profile.accountStatus;
      }
      refreshCustomerTableAfterProfileUpdate();
    }
    resetButtonLoading(btn);
    showToast('Demo payment recorded successfully.');
    openCustomerProfile(customerId);
  }, 500);
}

function refreshCustomerTableAfterProfileUpdate() {
  filterCust();
}

function exportCustomerProfileCsv(customerId) {
  let stored = JSON.parse(localStorage.getItem('customerProfileDemoData')) || {};
  const p = stored[customerId];
  if (!p) return;

  let csv = 'CUSTOMER PROFILE\\n';
  csv += 'Customer ID,Name,Contact,Address,Branch,Account Status\\n';
  csv += `${p.customerId},"${p.fullName}","${p.contact}","${p.address}","${p.branch}",${p.accountStatus}\\n\\n`;
  
  csv += 'BALANCE SUMMARY\\n';
  csv += 'Total Purchased,Total Paid,Remaining Balance,Next Due Date,Monthly Due\\n';
  csv += `${p.totalPurchased},${p.totalPaid},${p.remainingBalance},${p.nextDueDate},${p.monthlyDue}\\n\\n`;

  csv += 'ORDERS\\n';
  csv += 'Order No,Date,Model,Qty,Price,Amount,Mode,Status\\n';
  p.orders.forEach(o => { csv += `${o.no},${o.date},"${o.model}",${o.qty},${o.price},${o.amount},${o.mode},${o.status}\\n`; });

  csv += '\\nPAYMENT HISTORY\\n';
  csv += 'Payment No,Date,Method,Ref,Amount,Applied To,Status\\n';
  p.payments.forEach(pay => { csv += `${pay.no},${pay.date},${pay.method},${pay.ref},${pay.amount},${pay.appliedTo},${pay.status}\\n`; });

  downloadFile('customer-' + p.customerId + '.csv', csv, 'text/csv;charset=utf-8');
  showToast('Customer profile exported successfully.');
}

function filterPO(){
  const q=document.getElementById('po-q').value.toLowerCase();
  const st=document.getElementById('po-status').value;
  const sup=document.getElementById('po-supplier').value;
  let d=poData.filter(r=>(!st||r.status===st)&&(!sup||r.supplier===sup)&&(!q||[r.no,r.supplier,r.model].join(' ').toLowerCase().includes(q)));
  document.getElementById('po-count').textContent=`${d.length} orders`;
  document.getElementById('po-ft').textContent=`${d.length} of ${poData.length} orders`;
  document.getElementById('po-tb').innerHTML=d.map(r=>`<tr class="row-link" onclick="openPOPanel(${JSON.stringify(r).replace(/"/g,'&quot;')})">
    <td class="mono">${r.no}</td><td class="mono dim">${r.date.replace(', 2026','')}</td>
    <td><strong>${r.supplier}</strong></td><td class="dim">${r.model}</td>
    <td class="mono" style="text-align:center">${r.qty}</td>
    <td class="amt">&#8369;${fmt(r.amount)}</td>
    <td class="mono dim">${r.delivery.replace(', 2026','')}</td>
    <td class="dim">${r.branch}</td><td>${badge(r.status)}</td>
  </tr>`).join('')||'<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-tertiary)">No orders found</td></tr>';
}

function filterAP(){
  const q=document.getElementById('ap-q').value.toLowerCase();
  const st=document.getElementById('ap-status').value;
  let d=apData.filter(r=>(!st||r.status===st)&&(!q||[r.inv,r.vendor,r.desc].join(' ').toLowerCase().includes(q)));
  document.getElementById('ap-count').textContent=`${d.length} invoices`;
  document.getElementById('ap-ft').textContent=`${d.length} of ${apData.length} invoices`;
  document.getElementById('ap-tb').innerHTML=d.map(r=>`<tr>
    <td class="mono">${r.inv}</td><td><strong>${r.vendor}</strong></td><td class="dim">${r.desc}</td>
    <td class="amt">&#8369;${fmt(r.amount)}</td>
    <td class="amt" style="color:var(--green)">&#8369;${fmt(r.paid)}</td>
    <td class="amt" style="${r.due>0?'color:var(--red)':''}">&#8369;${fmt(r.due)}</td>
    <td class="mono dim">${r.invDate.replace(', 2026','')}</td>
    <td class="mono dim">${r.dueDate.replace(', 2026','')}</td>
    <td>${badge(r.status)}</td>
  </tr>`).join('');
}

function filterGL(){
  const q=document.getElementById('gl-q').value.toLowerCase();
  const t=document.getElementById('gl-type').value;
  let d=glData.filter(r=>(!t||r.type===t)&&(!q||[r.ref,r.account,r.desc].join(' ').toLowerCase().includes(q)));
  document.getElementById('gl-count').textContent=`${d.length} entries`;
  document.getElementById('gl-tb').innerHTML=d.map(r=>`<tr>
    <td class="mono dim">${r.date}</td><td class="mono">${r.ref}</td>
    <td class="dim" style="max-width:160px;overflow:hidden;text-overflow:ellipsis">${r.account}</td>
    <td class="dim">${r.desc}</td>
    <td class="amt" style="color:var(--red)">&#8369;${fmt(r.debit)}</td>
    <td class="amt" style="color:var(--green)">&#8369;${fmt(r.credit)}</td>
  </tr>`).join('');
}

// ===== PANELS =====
function openInvPanel(r){
  document.getElementById('cm-title').textContent=r.id+' — '+r.model;
  document.getElementById('cm-body').innerHTML=`
    <div class="sp-section"><div class="sp-section-label">Unit Information</div>
      <div class="sp-row"><span class="sp-key">Unit ID</span><span class="sp-val mono">${r.id}</span></div>
      <div class="sp-row"><span class="sp-key">Brand</span><span class="sp-val">${r.brand}</span></div>
      <div class="sp-row"><span class="sp-key">Model</span><span class="sp-val">${r.model}</span></div>
      <div class="sp-row"><span class="sp-key">Color</span><span class="sp-val">${r.color}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(r.status)}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Serial Numbers</div>
      <div class="sp-row"><span class="sp-key">Engine No.</span><span class="sp-val mono">${r.engine}</span></div>
      <div class="sp-row"><span class="sp-key">Chassis No.</span><span class="sp-val mono">${r.chassis}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Pricing & Location</div>
      <div class="sp-row"><span class="sp-key">SRP</span><span class="sp-val mono">&#8369;${fmt(r.price)}</span></div>
      <div class="sp-row"><span class="sp-key">Branch</span><span class="sp-val">${r.branch}</span></div>
      <div class="sp-row"><span class="sp-key">Date Received</span><span class="sp-val mono">${r.date}</span></div>
    </div>
    <div class="sp-actions">
      <button class="btn btn-sm btn-primary">Create Sales Order</button>
      <button class="btn btn-sm">Transfer Unit</button>
      <button class="btn btn-sm">Edit</button>
    </div>`;
  openCenterModal();
}

function openSOPanel(r){
  document.getElementById('cm-title').textContent=r.no;
  document.getElementById('cm-body').innerHTML=`
    <div class="sp-section"><div class="sp-section-label">Order Details</div>
      <div class="sp-row"><span class="sp-key">Order No.</span><span class="sp-val mono">${r.no}</span></div>
      <div class="sp-row"><span class="sp-key">Date</span><span class="sp-val mono">${r.date}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(r.status)}</span></div>
      <div class="sp-row"><span class="sp-key">Branch</span><span class="sp-val">${r.branch}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Customer & Unit</div>
      <div class="sp-row"><span class="sp-key">Customer</span><span class="sp-val">${r.customer}</span></div>
      <div class="sp-row"><span class="sp-key">Model</span><span class="sp-val">${r.model}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Payment</div>
      <div class="sp-row"><span class="sp-key">SRP</span><span class="sp-val mono">&#8369;${fmt(r.srp)}</span></div>
      <div class="sp-row"><span class="sp-key">Down Payment</span><span class="sp-val mono">&#8369;${fmt(r.dp)}</span></div>
      <div class="sp-row"><span class="sp-key">Balance</span><span class="sp-val mono">&#8369;${fmt(r.srp-r.dp)}</span></div>
      <div class="sp-row"><span class="sp-key">Mode</span><span class="sp-val">${r.mode}</span></div>
    </div>
    <div class="sp-actions">
      <button class="btn btn-sm btn-primary">Print OR</button>
      <button class="btn btn-sm">View Account</button>
    </div>`;
  openCenterModal();
}

function openPOPanel(r){
  document.getElementById('cm-title').textContent=r.no;
  document.getElementById('cm-body').innerHTML=`
    <div class="sp-section"><div class="sp-section-label">Purchase Order</div>
      <div class="sp-row"><span class="sp-key">PO No.</span><span class="sp-val mono">${r.no}</span></div>
      <div class="sp-row"><span class="sp-key">Date Issued</span><span class="sp-val mono">${r.date}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(r.status)}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Supplier & Units</div>
      <div class="sp-row"><span class="sp-key">Supplier</span><span class="sp-val">${r.supplier}</span></div>
      <div class="sp-row"><span class="sp-key">Models</span><span class="sp-val">${r.model}</span></div>
      <div class="sp-row"><span class="sp-key">Quantity</span><span class="sp-val mono">${r.qty} units</span></div>
      <div class="sp-row"><span class="sp-key">Total Amount</span><span class="sp-val mono">&#8369;${fmt(r.amount)}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Delivery</div>
      <div class="sp-row"><span class="sp-key">Expected Delivery</span><span class="sp-val mono">${r.delivery}</span></div>
      <div class="sp-row"><span class="sp-key">Destination</span><span class="sp-val">${r.branch}</span></div>
    </div>
    <div class="sp-actions">
      <button class="btn btn-sm btn-primary">Mark Delivered</button>
      <button class="btn btn-sm">Print PO</button>
    </div>`;
  openCenterModal();
}

function openPayrollPanel(no){
  const r=payrollData.find(x=>x.no===no);
  if(!r)return;
  document.getElementById('cm-title').textContent=r.no;
  document.getElementById('cm-body').innerHTML=`
    <div class="sp-section"><div class="sp-section-label">Employee Payroll</div>
      <div class="sp-row"><span class="sp-key">Employee</span><span class="sp-val">${r.employee}</span></div>
      <div class="sp-row"><span class="sp-key">Employee ID</span><span class="sp-val mono">${r.employeeId}</span></div>
      <div class="sp-row"><span class="sp-key">Position</span><span class="sp-val">${r.position}</span></div>
      <div class="sp-row"><span class="sp-key">Branch</span><span class="sp-val">${r.branch}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(r.status)}</span></div>
    </div>
    <div class="sp-section"><div class="sp-section-label">Salary Computation</div>
      <div class="sp-row"><span class="sp-key">Basic Pay</span><span class="sp-val mono">₱${fmt(r.basic)}</span></div>
      <div class="sp-row"><span class="sp-key">Overtime</span><span class="sp-val mono">₱${fmt(r.overtime)}</span></div>
      <div class="sp-row"><span class="sp-key">Allowance</span><span class="sp-val mono">₱${fmt(r.allowance)}</span></div>
      <div class="sp-row"><span class="sp-key">Deductions</span><span class="sp-val mono">₱${fmt(r.deductions)}</span></div>
      <div class="sp-row"><span class="sp-key">Net Pay</span><span class="sp-val mono">₱${fmt(r.net)}</span></div>
    </div>
    <div class="sp-actions">
      <button class="btn btn-sm btn-primary" onclick="updateOnePayroll('${r.no}','Approved')">Approve</button>
      <button class="btn btn-sm" onclick="updateOnePayroll('${r.no}','Released')">Mark Released</button>
      <button class="btn btn-sm">Print Payslip</button>
    </div>`;
  openCenterModal();
}

// ===== PANEL CONTROL =====
function openPanel(){
  document.getElementById('sp-overlay').classList.add('open');
  setTimeout(()=>document.getElementById('side-panel').classList.add('open'),10);
}
function closePanel(){
  document.getElementById('side-panel').classList.remove('open');
  setTimeout(()=>document.getElementById('sp-overlay').classList.remove('open'),200);
}

// ===== CENTER MODAL =====
function openCenterModal(titleOrId, html, footerHtml, options) {
  // New signature: openCenterModal(title, bodyHtml, footerHtml, options)
  if (html !== undefined) {
    const overlay = document.getElementById('cm-overlay');
    const modal = overlay?.querySelector('.demo-center-modal');
    document.getElementById('cm-title').textContent = titleOrId;
    const bodyEl = document.getElementById('cm-body');
    bodyEl.innerHTML = `<div class="sp-section">${html}</div>`;
    // Footer
    let footer = overlay?.querySelector('.cm-footer');
    if (footerHtml) {
      if (!footer) {
        footer = document.createElement('div');
        footer.className = 'cm-footer';
        modal?.appendChild(footer);
      }
      footer.innerHTML = footerHtml;
      footer.style.display = 'flex';
    } else if (footer) {
      footer.style.display = 'none';
    }
    // Width option
    if (modal && options?.width) {
      modal.style.width = options.width;
    } else if (modal) {
      modal.style.width = '';
    }
    overlay?.classList.add('open');
    return;
  }
  // Legacy signature: openCenterModal(id)
  document.getElementById(titleOrId || 'cm-overlay')?.classList.add('open');
}
function closeCenterModal(id) {
  if (id) {
    document.getElementById(id)?.classList.remove('open');
  } else {
    document.getElementById('cm-overlay')?.classList.remove('open');
    // Reset width
    const modal = document.querySelector('#cm-overlay .demo-center-modal');
    if (modal) modal.style.width = '';
  }
}

// ===== NAVIGATION =====
const navMeta={
  dashboard:{title:'Dashboard',page:'Overview'},
  inventory:{title:'Inventory',page:'Motor Units'},
  'inventory-menu':{title:'Inventory',page:'Menu View'},
  sales:{title:'Sales Orders',page:'Transactions'},
  'sales-menu':{title:'Sales Orders',page:'Menu View'},
  receivables:{title:'Receivables',page:'Installment Accounts'},
  'receivables-menu':{title:'Receivables',page:'Menu View'},
  customers:{title:'Customers',page:'Customer Records'},
  purchases:{title:'Purchase Orders',page:'Procurement'},
  'purchases-menu':{title:'Purchases',page:'Menu View'},
  payables:{title:'Payables',page:'Accounts Payable'},
  'payables-menu':{title:'Payables',page:'Menu View'},
  cashfund:{title:'Cash Fund Management',page:'Finance'},
  'cashfund-menu':{title:'Cash Fund Management',page:'Menu View'},
  banking:{title:'Banking',page:'Bank Accounts & Transactions'},
  'banking-menu':{title:'Banking',page:'Menu View'},
  gl:{title:'General Ledger',page:'Finance'},
  'gl-menu':{title:'General Ledger',page:'Menu View'},
  favorites:{title:'Favorites',page:'Services & Shortcuts'},
  'equipment-menu':{title:'Equipment',page:'Menu View'},
  orcr:{title:'OTHER FINANCING OR/CR TRANSMITTAL',page:'Report Process'},
  timeexp:{title:'Time & Expenses',page:'HR & Admin'},
  'timeexp-menu':{title:'Time and Expenses',page:'Menu View'},
  payroll:{title:'Payroll Management',page:'HR & Admin'},
  branches:{title:'Branch Directory',page:'Administration'},
};
function nav(id){
  document.querySelectorAll('.sb-nav a').forEach(a=>a.classList.remove('active'));
  const activeNavId=id.endsWith('-menu')?id.replace('-menu',''):id;
  document.getElementById('nav-'+activeNavId)?.classList.add('active');
  if(id==='orcr')document.getElementById('nav-favorites')?.classList.add('active');
  document.querySelectorAll('.module').forEach(m=>m.classList.remove('active'));
  document.getElementById('mod-'+id)?.classList.add('active');
  const m=navMeta[id]||{title:id,page:id};
  document.getElementById('tb-title').textContent=m.title;
  document.getElementById('tb-page').textContent=m.page;
  closePanel();
  closeCenterModal();
  closeCenterModal('migration-modal-overlay');
  closeCenterModal('orcr-preview-overlay');
}

// ===== ERP MENU VIEWS =====
function renderErpMenuLink(item, heading, typePrefix = ''){
  const link=typeof item==='string'?{label:item}:item;
  const click=link.nav?` onclick="nav('${link.nav}')"`:'';
  
  let type = 'generic';
  if(heading) {
    const headingLower = heading.toLowerCase();
    if (headingLower.includes('report')) type = 'report';
    else if (headingLower.includes('process')) type = 'process';
    else if (headingLower.includes('print')) type = 'printed-form';
    else if (headingLower.includes('profile')) type = 'profile';
    else if (headingLower.includes('preference')) type = 'preference';
    else if (headingLower.includes('transaction')) type = 'transaction';
    else if (headingLower.includes('inquir')) type = 'inquiry';
    else if (headingLower.includes('service')) type = 'service';
    else if (headingLower.includes('integration')) type = 'integration';
  }
  type = typePrefix + type;

  return `<button class="erp-menu-link"${click} data-action="${esc(link.label)}" data-type="${type}" ${link.nav ? `data-target="${link.nav}"` : ''} type="button">${esc(link.label)}</button>`;
}
function renderErpMenuPage(id,config){
  const host=document.getElementById(`mod-${id}`);
  if(!host)return;
  const typePrefix = id === 'receivables-menu' ? 'ar-' : (id === 'purchases-menu' ? 'po-' : (id === 'payables-menu' ? 'ap-' : (id === 'inventory-menu' ? 'in-' : '')));
  const shortcutHtml=(config.shortcuts||[]).length?`
    <div class="erp-shortcuts">
      ${config.shortcuts.map((label,index)=>`
        <button class="erp-shortcut-card" type="button" data-action="${esc(label)}" data-type="${typePrefix}shortcut">
          <span class="erp-shortcut-icon">${index+1}</span>
          <strong>${esc(label)}</strong>
        </button>
      `).join('')}
    </div>`:'';
  host.innerHTML=`
    <div class="erp-menu-page">
      <div class="erp-menu-intro">
        <div>
          <h2>${esc(config.title)}</h2>
          <p>ERP menu view for transactions, profiles, processes, inquiries, reports, and setup links.</p>
        </div>
        ${config.dataView?`<button class="btn btn-sm" type="button" data-view-target="${config.dataView}">Data View</button>`:`<button class="btn btn-sm" type="button" onclick="showToast('Demo only: Data view will be available in full implementation.')">Data View</button>`}
      </div>
      ${shortcutHtml}
      <div class="erp-menu-grid">
        ${Object.entries(config.sections).map(([heading,links])=>`
          <section class="erp-menu-section">
            <h3 class="erp-menu-heading">${esc(heading)}</h3>
            <div class="erp-link-list">${links.map(l => renderErpMenuLink(l, heading, typePrefix)).join('')}</div>
          </section>
        `).join('')}
      </div>
    </div>
  `;
}
function renderErpMenuPages(){
  Object.entries(erpMenuModules).forEach(([id,config])=>renderErpMenuPage(id,config));
}
function addMenuViewButtons(){
  const map={inventory:'inventory-menu',sales:'sales-menu',receivables:'receivables-menu',purchases:'purchases-menu',payables:'payables-menu',cashfund:'cashfund-menu',banking:'banking-menu',gl:'gl-menu',timeexp:'timeexp-menu'};
  Object.entries(map).forEach(([dataId,menuId])=>{
    const actions=document.querySelector(`#mod-${dataId} .page-actions`);
    if(!actions||actions.querySelector(`[data-menu-view="${menuId}"]`))return;
    actions.insertAdjacentHTML('afterbegin',`<button class="btn btn-sm" data-menu-view="${menuId}" type="button" onclick="nav('${menuId}')">Menu View</button>`);
  });
}

// ===== OTHER FINANCING OR/CR TRANSMITTAL =====
function esc(value){
  return String(value??'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
}
function orcrGeneratedNow(){
  return new Date().toLocaleString('en-PH',{year:'numeric',month:'short',day:'2-digit',hour:'2-digit',minute:'2-digit',hour12:true});
}
function getOrcrBank(){
  return document.getElementById('orcr-bank')?.value||'All Banks';
}
function getOrcrCutoff(){
  const value=document.getElementById('orcr-cutoff')?.value||'';
  if(!value)return 'Not specified';
  return new Date(`${value}T00:00:00`).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'2-digit'});
}
function renderOrcrRows(rows){
  const tbody=document.getElementById('orcr-tbody');
  if(!tbody)return;
  tbody.innerHTML=rows.map(row=>`<tr>
    <td class="mono">${esc(row.unitNo)}</td>
    <td><strong>${esc(row.customerName)}</strong></td>
    <td>${esc(row.financingBank)}</td>
    <td class="mono">${esc(row.orNumber)}</td>
    <td class="mono">${esc(row.crNumber)}</td>
    <td class="mono">${esc(row.salesOrderNo)}</td>
    <td class="mono">${esc(row.invoiceNo)}</td>
    <td class="dim">${esc(row.releaseDate)}</td>
    <td>${badge(row.status)}</td>
    <td class="dim">${esc(row.remarks)}</td>
  </tr>`).join('')||'<tr><td colspan="10" style="text-align:center;padding:32px;color:var(--text-tertiary)">No records found for the selected financing bank</td></tr>';
}
function runOrcrReport(){
  const runRow=document.getElementById('orcr-run-row');
  const runBtn=document.getElementById('orcr-run-btn');
  runRow?.classList.add('loading');
  if(runBtn){
    runBtn.classList.add('busy');
    runBtn.textContent='RUNNING...';
  }
  setTimeout(()=>{
    const bank=getOrcrBank();
    currentOrcrRows=bank==='All Banks'?financingReportData.slice():financingReportData.filter(row=>row.financingBank===bank);
    currentOrcrGenerated=orcrGeneratedNow();
    renderOrcrRows(currentOrcrRows);
    document.getElementById('orcr-total').textContent=currentOrcrRows.length;
    document.getElementById('orcr-generated').textContent=currentOrcrGenerated;
    document.getElementById('orcr-summary')?.classList.add('open');
    document.getElementById('orcr-result-table')?.classList.add('open');
    document.getElementById('orcr-empty').style.display='none';
    runRow?.classList.remove('loading');
    if(runBtn){
      runBtn.classList.remove('busy');
      runBtn.textContent='RUN';
    }
    showToast('OTHER FINANCING OR/CR TRANSMITTAL generated');
  },1000);
}
function exportOrcrCsv(){
  if(!currentOrcrRows.length){
    showToast('Run the report before exporting');
    return;
  }
  const headers=['unitNo','customerName','financingBank','orNumber','crNumber','salesOrderNo','invoiceNo','releaseDate','status','remarks'];
  const label=getOrcrBank().toLowerCase().replace(/[^a-z0-9]+/g,'-');
  downloadFile(`other-financing-orcr-transmittal-${label}.csv`,toCsv(currentOrcrRows,headers),'text/csv;charset=utf-8');
  showToast('OR/CR transmittal CSV exported');
}
function orcrPreviewTable(rows){
  return `<div class="report-preview-table"><table>
    <thead><tr><th>Unit No.</th><th>Customer Name</th><th>Financing Bank</th><th>OR Number</th><th>CR Number</th><th>Sales Order No.</th><th>Invoice No.</th><th>Release Date</th><th>Status</th><th>Remarks</th></tr></thead>
    <tbody>${rows.map(row=>`<tr>
      <td class="mono">${esc(row.unitNo)}</td><td>${esc(row.customerName)}</td><td>${esc(row.financingBank)}</td>
      <td class="mono">${esc(row.orNumber)}</td><td class="mono">${esc(row.crNumber)}</td><td class="mono">${esc(row.salesOrderNo)}</td>
      <td class="mono">${esc(row.invoiceNo)}</td><td>${esc(row.releaseDate)}</td><td>${esc(row.status)}</td><td>${esc(row.remarks)}</td>
    </tr>`).join('')}</tbody>
  </table></div>`;
}
function openOrcrPreview(){
  if(!currentOrcrRows.length){
    showToast('Run the report before opening preview');
    return;
  }
  document.getElementById('orcr-preview-body').innerHTML=`
    <div class="report-preview-head">
      <div>
        <h2>NEXII BSM DEMO</h2>
        <p id="orcr-preview-title">OTHER FINANCING OR/CR TRANSMITTAL</p>
      </div>
      <div class="report-preview-meta">
        <div>Cutoff Date: <strong>${esc(getOrcrCutoff())}</strong></div>
        <div>Financing Bank: <strong>${esc(getOrcrBank())}</strong></div>
        <div>Generated Date: <strong>${esc(currentOrcrGenerated)}</strong></div>
        <div>Total Records: <strong>${currentOrcrRows.length}</strong></div>
      </div>
    </div>
    ${orcrPreviewTable(currentOrcrRows)}
  `;
  openCenterModal('orcr-preview-overlay');
}
function closeOrcrPreview(){
  closeCenterModal('orcr-preview-overlay');
}
function emailOrcrReport(){
  showToast('Demo only: Report email has been prepared successfully.');
}

// ===== TIME & EXPENSE TABS =====
function teTab(id,el){
  document.querySelectorAll('.te-tab').forEach(t=>t.classList.remove('active'));
  document.querySelectorAll('.te-panel').forEach(p=>p.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('te-'+id).classList.add('active');
}

// ===== LIVE ACTIONS =====
let toastTimer=null;
function showToast(msg){
  let t=document.getElementById('toast');
  if(!t){
    t=document.createElement('div');
    t.id='toast';
    t.className='toast';
    document.body.appendChild(t);
  }
  t.textContent=msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer=setTimeout(()=>t.classList.remove('show'),2200);
}
function flashButton(btn,label){
  if(!btn)return;
  const old=btn.textContent;
  btn.classList.add('busy','flash');
  btn.textContent=label||'Working...';
  setTimeout(()=>{btn.classList.remove('busy');btn.textContent=old;},520);
  setTimeout(()=>btn.classList.remove('flash'),900);
}

function activeModuleId(){
  return document.querySelector('.module.active')?.id?.replace('mod-','')||'dashboard';
}

function openActionPanel(title,sections,actions=''){
  document.getElementById('cm-title').textContent=title;
  document.getElementById('cm-body').innerHTML=sections.map(section=>`
    <div class="sp-section">
      <div class="sp-section-label">${section.label}</div>
      ${section.rows.map(row=>`<div class="sp-row"><span class="sp-key">${row[0]}</span><span class="sp-val ${row[2]||''}">${row[1]}</span></div>`).join('')}
    </div>
  `).join('')+(actions?`<div class="sp-actions">${actions}</div>`:'');
  const footer = document.querySelector('#cm-overlay .cm-footer');
  if (footer) footer.style.display = 'none';
  const modal = document.querySelector('#cm-overlay .demo-center-modal');
  if (modal) modal.style.width = '';
  openCenterModal();
}

function refreshCurrentModule(){
  renderDashboard();filterInv();filterSO();filterAR();filterCust();filterPO();filterAP();renderCashFund();filterGL();renderEmployees();renderPayroll();renderBranches();
  showToast('Current module refreshed');
}

function addDemoUnit(){
  const r={id:`UNIT-DEMO-${pad(invData.length+1,4)}`,brand:'Honda',model:'Demo ADV 160',color:'Pearl White',engine:`ENG-DEMO-${pad(9000+invData.length,5)}`,chassis:`CHS-DEMO-${pad(9500+invData.length,5)}`,branch:branchData[0].name,status:'Available',price:166900,date:'May 14, 2026'};
  invData.unshift(r);refreshBranchInventory();clearImportFilters('inventory');filterInv();renderDashboard();renderBranches();
  openInvPanel(r);showToast('Demo unit added to inventory');
}

function addDemoOrder(){
  openNewSalesOrderDemo();
}

function recordDemoPayment(){
  const r=arData.find(x=>x.status!=='Paid')||arData[0];
  if(!r)return;
  r.balance=Math.max(0,r.balance-r.monthly);
  r.overdue=0;
  r.status=r.balance?'Current':'Paid';
  filterAR();
  openActionPanel('Payment Recorded',[
    {label:'Account',rows:[['Account No.',r.no,'mono'],['Customer',r.customer],['Updated Status',badge(r.status)],['Remaining Balance',`₱${fmt(r.balance)}`,'mono']]},
    {label:'Receipt',rows:[['Amount Paid',`₱${fmt(r.monthly)}`,'mono'],['Posting Date','May 14, 2026','mono'],['Collected By','Demo Collector']]}
  ],'<button class="btn btn-sm btn-primary">Print Receipt</button><button class="btn btn-sm">Send SMS</button>');
  showToast('Payment posted to receivables');
}

function addDemoCustomer(){
  const r={id:`CUS-DEMO-${pad(custData.length+1,4)}`,name:'Demo Customer - Adrian Villanueva',contact:'+63 917 700 2026',address:'BGC, Taguig City',branch:branchData[0].name,units:0,total:0,status:'Active'};
  custData.unshift(r);clearImportFilters('customers');filterCust();
  openActionPanel('Customer Created',[
    {label:'Customer Profile',rows:[['Customer ID',r.id,'mono'],['Full Name',r.name],['Contact',r.contact,'mono'],['Branch',r.branch],['Status',badge(r.status)]]}
  ],'<button class="btn btn-sm btn-primary">Create Sales Order</button><button class="btn btn-sm">Open KYC</button>');
  showToast('Demo customer added');
}

function addDemoPO(){
  const r={no:`PO-DEMO-${pad(poData.length+1,4)}`,date:'May 14, 2026',supplier:'Honda Philippines',model:'Honda ADV 160',qty:10,amount:1440000,delivery:'May 28, 2026',branch:branchData[0].name,status:'Pending'};
  poData.unshift(r);clearImportFilters('purchases');filterPO();openPOPanel(r);showToast('Demo purchase order created');
}

function recordDemoPayable(){
  const r=apData.find(x=>x.due>0)||apData[0];
  if(!r)return;
  const payment=Math.min(r.due,50000);
  r.paid+=payment;r.due-=payment;r.status=r.due?'Partial':'Paid';
  filterAP();
  openActionPanel('Supplier Payment Posted',[
    {label:'Invoice',rows:[['Invoice No.',r.inv,'mono'],['Vendor',r.vendor],['Payment',`₱${fmt(payment)}`,'mono'],['Remaining Due',`₱${fmt(r.due)}`,'mono'],['Status',badge(r.status)]]}
  ],'<button class="btn btn-sm btn-primary">Print Voucher</button><button class="btn btn-sm">Email Supplier</button>');
  showToast('Supplier payment recorded');
}

function addDemoFundTransfer(){
  const r={date:'05/14/26',branch:branchData[0].name,ref:`CF-DEMO-${pad(cashFundData.length+1,4)}`,desc:'Demo branch fund replenishment',cashIn:30000,cashOut:0,balance:branchData[0].cashFund+30000,recordedBy:'Patricia Mendoza'};
  cashFundData.unshift(r);branchData[0].cashFund=r.balance;renderCashFund();renderBranches();
  openActionPanel('Fund Transfer Created',[
    {label:'Transfer Details',rows:[['Reference',r.ref,'mono'],['Branch',r.branch],['Cash In',`₱${fmt(r.cashIn)}`,'mono'],['New Balance',`₱${fmt(r.balance)}`,'mono']]}
  ],'<button class="btn btn-sm btn-primary">Approve Transfer</button><button class="btn btn-sm">Print Voucher</button>');
  showToast('Cash fund transfer added');
}

function addDemoBankTransaction(){
  const r={date:'May 14',bank:'BPI',ref:`BPI-DEMO-${pad(Date.now()%10000,4)}`,desc:'Demo client collection deposit',debit:0,credit:125000,balance:12605500,type:'Credit'};
  const bankTb=document.getElementById('bank-tb');
  if(bankTb)bankTb.insertAdjacentHTML('afterbegin',`<tr><td class="mono dim">${r.date}</td><td>${r.bank}</td><td class="mono">${r.ref}</td><td>${r.desc}</td><td class="dim">—</td><td class="amt" style="color:var(--green)">${fmt(r.credit)}</td><td class="amt">${fmt(r.balance)}</td><td>${badge(r.type)}</td></tr>`);
  openActionPanel('Bank Transaction Added',[
    {label:'Transaction',rows:[['Bank',r.bank],['Reference',r.ref,'mono'],['Description',r.desc],['Credit',`₱${fmt(r.credit)}`,'mono'],['Type',badge(r.type)]]}
  ],'<button class="btn btn-sm btn-primary">Reconcile</button><button class="btn btn-sm">Attach Proof</button>');
  showToast('Bank transaction added');
}

function addDemoJournalEntry(){
  const r={date:'05/14/26',ref:`JE-DEMO-${pad(glData.length+1,4)}`,account:'Cash in Bank / Sales Revenue',desc:'Demo manual journal entry',debit:125000,credit:125000,type:'Sales'};
  glData.unshift(r);clearImportFilters('accounting');filterGL();
  openActionPanel('Journal Entry Created',[
    {label:'Entry',rows:[['Reference',r.ref,'mono'],['Account',r.account],['Debit',`₱${fmt(r.debit)}`,'mono'],['Credit',`₱${fmt(r.credit)}`,'mono'],['Type',badge(r.type)]]}
  ],'<button class="btn btn-sm btn-primary">Post Entry</button><button class="btn btn-sm">Print Journal</button>');
  showToast('Journal entry added');
}

function submitDemoExpense(){
  const r={no:`EXP-DEMO-${pad(expenseData.length+1,4)}`,employee:'Demo Staff - Carla Dizon',branch:branchData[0].name,category:'Transportation',desc:'Demo client visit expense',amount:1850,date:'05/14/26',status:'Pending'};
  expenseData.unshift(r);renderEmployees();
  openActionPanel('Expense Submitted',[
    {label:'Claim',rows:[['Claim No.',r.no,'mono'],['Employee',r.employee],['Category',r.category],['Amount',`₱${fmt(r.amount)}`,'mono'],['Status',badge(r.status)]]}
  ],'<button class="btn btn-sm btn-primary">Approve Claim</button><button class="btn btn-sm">Request Receipt</button>');
  showToast('Expense claim submitted');
}

function addDemoBranch(){
  const r={name:'Demo Expansion Branch',region:'Luzon',address:'Demo Business District, Pampanga',manager:'Demo Manager',contact:'+63 917 888 2026',inventory:0,sold:0,cashFund:100000,status:'Normal'};
  branchData.unshift(r);populateBranchSelects();renderBranches();
  openActionPanel('Branch Added',[
    {label:'Branch Profile',rows:[['Branch',r.name],['Region',r.region],['Address',r.address],['Manager',r.manager],['Cash Fund',`₱${fmt(r.cashFund)}`,'mono'],['Status',badge(r.status)]]}
  ],'<button class="btn btn-sm btn-primary">Assign Inventory</button><button class="btn btn-sm">Open Branch</button>');
  showToast('Demo branch added');
}

function manageAlerts(){
  openActionPanel('Alert Management',[
    {label:'Active Alerts',rows:[['Critical Stock','1 branch below threshold'],['Finance Alerts','2 payment issues'],['Receivables','6 accounts need follow-up'],['Next Action','Assign alert owners']]}
  ],'<button class="btn btn-sm btn-primary">Assign Owners</button><button class="btn btn-sm">Snooze Low Priority</button>');
}

function reconcileBank(){
  openActionPanel('Bank Reconciliation',[
    {label:'Reconciliation Run',rows:[['Matched Transactions','42'],['Exceptions','3'],['Bank','BPI / BDO / MetroBank'],['Status',badge('Processing')]]}
  ],'<button class="btn btn-sm btn-primary">Approve Reconciliation</button><button class="btn btn-sm">Export Exceptions</button>');
  showToast('Bank reconciliation prepared');
}

function showHistory(title='Full History'){
  openActionPanel(title,[
    {label:'Audit Trail',rows:[['May 14, 2026','Demo record created'],['May 14, 2026','Workflow status updated'],['May 13, 2026','Export generated'],['May 12, 2026','Manager reviewed']]}
  ],'<button class="btn btn-sm btn-primary">Export History</button><button class="btn btn-sm">Print</button>');
}

function refreshPayroll(){
  payrollData=payrollData.map((r,i)=>i%17===0&&r.status==='Processing'?{...r,status:'Released'}:r);
  renderPayroll();
  showToast('Payroll live data refreshed');
}
function markPayrollReviewed(){
  payrollData=payrollData.map((r,i)=>r.status==='Draft'&&i%2===0?{...r,status:'Reviewed'}:r);
  renderPayroll();
  showToast('Draft payroll records marked for review');
}
function approvePayroll(){
  payrollData=payrollData.map(r=>['Draft','Reviewed'].includes(r.status)?{...r,status:'Approved'}:r);
  renderPayroll();
  showToast('Payroll run approved');
}
function processPayroll(){
  payrollData=payrollData.map(r=>r.status==='Approved'?{...r,status:'Processing'}:r);
  renderPayroll();
  showToast('Payroll payout processing started');
}
function updateOnePayroll(no,status){
  payrollData=payrollData.map(r=>r.no===no?{...r,status}:r);
  renderPayroll();
  openPayrollPanel(no);
  showToast(`${no} updated to ${status}`);
}
function exportPayroll(){
  exportCsv('payroll');
}

function liveAction(btn){
  const label=(btn.textContent||'Action').trim().replace(/\s+/g,' ');
  const clean=label.replace(/^[+]\s*/,'');
  const module=activeModuleId();
  if(label==='Refresh'){refreshCurrentModule();return}
  if(label.includes('Manage')){manageAlerts();return}
  if(label.includes('View Full History')){showHistory('Cash Fund History');return}
  if(label==='Reconcile'){reconcileBank();return}
  if(label.includes('Add Unit')){addDemoUnit();return}
  if(label.includes('New Order')){addDemoOrder();return}
  if(label.includes('Record Payment')){module==='payables'?recordDemoPayable():recordDemoPayment();return}
  if(label.includes('New Customer')){addDemoCustomer();return}
  if(label.includes('New PO')){addDemoPO();return}
  if(label.includes('Fund Transfer')){addDemoFundTransfer();return}
  if(label.includes('New Transaction')){addDemoBankTransaction();return}
  if(label.includes('Journal Entry')){addDemoJournalEntry();return}
  if(label.includes('Submit Expense')){submitDemoExpense();return}
  if(label.includes('Add Branch')){addDemoBranch();return}
  if(label.includes('Add Account')){
    openActionPanel('Chart of Account Added',[
      {label:'Account',rows:[['Code','5400','mono'],['Name','Demo Operating Expense'],['Type','Expense'],['Status',badge('Active')]]}
    ],'<button class="btn btn-sm btn-primary">Save Account</button><button class="btn btn-sm">View Ledger</button>');
    showToast('Demo chart account prepared');
    return;
  }
  if(label.includes('Prev')||label.includes('Next')){
    showToast(`${label.replace(/[←→]/g,'').trim()} page loaded`);
    return;
  }
  if(label.includes('Print')||label.includes('Payslip')){
    downloadFile(`nexii-${clean.toLowerCase().replace(/[^a-z0-9]+/g,'-')}.txt`,`${clean}\nGenerated from NEXII BSM Demo on ${new Date().toLocaleString('en-PH')}`);
    showToast(`${clean} downloaded`);
    return;
  }
  if(label.includes('Export')){
    downloadFile(`nexii-${clean.toLowerCase().replace(/[^a-z0-9]+/g,'-')}.txt`,`${clean}\nGenerated from NEXII BSM Demo on ${new Date().toLocaleString('en-PH')}`);
    showToast(`${clean} downloaded`);
    return;
  }
  if(label.includes('Transfer Unit')){
    openActionPanel('Unit Transfer',[
      {label:'Transfer Request',rows:[['From','Manila Main'],['To','Quezon City North'],['Approval',badge('Pending')],['ETA','May 16, 2026']]}
    ],'<button class="btn btn-sm btn-primary">Approve Transfer</button><button class="btn btn-sm">Print Gate Pass</button>');
    return;
  }
  if(label.includes('Create Sales Order')){addDemoOrder();return}
  if(label.includes('View Account')){
    nav('receivables');
    showToast('Receivables account opened');
    return;
  }
  if(label.includes('Mark Delivered')){
    showToast('Purchase order marked delivered');
    return;
  }
  if(label.includes('Approve')||label.includes('Post')||label.includes('Assign')||label.includes('Save')||label.includes('Send')||label.includes('Attach')||label.includes('Request')||label.includes('Open')){
    showToast(`${clean} completed`);
    return;
  }
  showToast(`${clean} completed in demo mode`);
}
function toggleTheme(){
  const dark=!document.body.classList.contains('dark');
  document.body.classList.toggle('dark',dark);
  document.getElementById('theme-label').textContent=dark?'Dark':'Light';
  try{localStorage.setItem('nexii-theme',dark?'dark':'light')}catch(e){}
  showToast(`${dark?'Dark':'Light'} mode enabled`);
}
function initTheme(){
  let saved='';
  try{saved=localStorage.getItem('nexii-theme')||''}catch(e){}
  if(saved==='dark')document.body.classList.add('dark');
  const label=document.getElementById('theme-label');
  if(label)label.textContent=document.body.classList.contains('dark')?'Dark':'Light';
}
if(document.addEventListener){
  document.addEventListener('click',e=>{
    const btn=e.target.closest?.('.btn');
    if(!btn||btn.closest('.sp-close'))return;
    if(btn.getAttribute('onclick'))return;
    flashButton(btn);
    liveAction(btn);
  });
  document.addEventListener('click', function(e) {
    const item = e.target.closest('[data-action]');
    if (item && !item.getAttribute('onclick')) {
      e.preventDefault();
      
      const action = item.dataset.action;
      const type = item.dataset.type;
      const target = item.dataset.target;
      
      if (type === 'process') {
        openProcessDemo(action);
        return;
      }
      
      if (type && type.startsWith('ar-')) {
        openReceivablesDemo(action, type);
        return;
      }
      
      if (type && type.startsWith('po-')) {
        openPurchasesDemo(action, type);
        return;
      }
      
      if (type && type.startsWith('ap-')) {
        openPayablesDemo(action, type);
        return;
      }
      
      if (type && type.startsWith('in-')) {
        openInventoryDemo(action, type);
        return;
      }
      
      handleDemoAction(action, type, target);
      return;
    }
  
    const dataBtn = e.target.closest('[data-view-target]');
    if (dataBtn) {
      e.preventDefault();
      const target = dataBtn.dataset.viewTarget;
      if (target) {
        if (document.getElementById(`mod-${target}`)) {
          if (typeof nav === 'function') nav(target);
        } else {
          const tName = target.charAt(0).toUpperCase() + target.slice(1);
          openCenterModal('Notice', `<div style="padding:40px;text-align:center">${tName} data view is available in the full implementation.</div>`, `<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`);
        }
      }
      return;
    }
  });
}

function handleDemoAction(action, type, target) {
  if (target) { nav(target); return; }
  if (action === 'OTHER FINANCING OR/CR TRANSMITTAL') { nav('orcr'); return; }
  if (action === 'CSB OR/CR TRANSMITTAL' || action === 'SUMISHO OR/CR TRANSMITTAL') { openReportDemo(action); return; }

  if (action === 'New Sales Order') return openNewSalesOrderDemo();
  if (action === 'New Quote') return openNewQuoteDemo();
  if (action === 'New Payment') return openNewPaymentDemo();
  if (action === 'New Customer') return openNewCustomerDemo();

  if (action === 'Sales Orders') { nav('sales'); return; }
  if (action === 'Invoices') return openInvoicesDemo();
  if (action === 'Shipments') return openShipmentsDemo();
  if (action === 'Customers') { nav('customers'); return; }
  if (action === 'Sales Prices') return openSalesPricesDemo();

  if (type === 'process') return openProcessDemo(action);
  if (type === 'report') return openReportDemo(action);
  if (type === 'printed-form') return openDemoPrintedForm(action);
  if (type === 'profile') return openDemoProfile(action);
  if (type === 'preference') return openDemoPreference(action);

  return openGenericDemo(action, type || 'Feature');
}

function openDemoPanel(title, html, actionsHtml) {
  document.getElementById('cm-title').textContent = title;
  document.getElementById('cm-body').innerHTML = `
    <div class="sp-section">
      ${html}
    </div>
    ${actionsHtml ? `<div class="sp-actions">${actionsHtml}</div>` : ''}
  `;
  // Hide the new-style footer if it exists
  const footer = document.querySelector('#cm-overlay .cm-footer');
  if (footer) footer.style.display = 'none';
  // Reset modal width
  const modal = document.querySelector('#cm-overlay .demo-center-modal');
  if (modal) modal.style.width = '';
  openCenterModal();
}

function openNewSalesOrderDemo() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Customer Name</label><input type="text" id="nso-customer" placeholder="Enter customer name">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Unit Model</label><select id="nso-model"><option>Honda ADV 160</option><option>Honda Click 125i</option><option>Yamaha NMAX</option><option>Yamaha Mio Gear</option><option>Kawasaki Ninja 400</option><option>Suzuki Raider R150</option></select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Branch</label><select id="nso-branch">${branches}</select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Payment Mode</label><select id="nso-mode"><option>Cash</option><option>Installment</option><option>Bank Financing</option></select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Salesperson</label><input type="text" placeholder="Enter salesperson name">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Down Payment</label><input type="number" id="nso-dp" value="0">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Estimated Total</label><input type="number" id="nso-total" value="166900">
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitNewSalesOrder()">Submit Order</button><button class="btn btn-sm" onclick="showToast('Demo only: sales order draft saved.')">Save Draft</button>`;
  openDemoPanel('New Sales Order', html, actions);
}

function submitNewSalesOrder() {
  const customerName = document.getElementById('nso-customer').value || 'Demo Customer';
  const unitModel = document.getElementById('nso-model').value;
  const branch = document.getElementById('nso-branch').value;
  const paymentMode = document.getElementById('nso-mode').value;
  const estimatedTotal = document.getElementById('nso-total').value || 0;
  const downPayment = document.getElementById('nso-dp').value || 0;

  if (typeof soData !== 'undefined') {
    const r = {
      no: `SO-DEMO-${pad(soData.length + 1, 4)}`,
      date: new Date().toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' }),
      customer: customerName,
      model: unitModel,
      srp: Number(estimatedTotal),
      dp: Number(downPayment),
      mode: paymentMode,
      branch: branch,
      status: 'Pending'
    };
    soData.unshift(r);
    if (typeof clearImportFilters === 'function') clearImportFilters('sales');
    if (typeof filterSO === 'function') filterSO();
    if (typeof renderDashboard === 'function') renderDashboard();
  }

  closeCenterModal();
  showToast('Demo sales order submitted successfully.');
}

function openNewQuoteDemo() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Customer Name</label><input type="text" placeholder="Enter customer name">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Unit Model</label><input type="text" placeholder="e.g. Honda ADV 160">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Branch</label><select>${branches}</select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Quote Amount</label><input type="number" value="0">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Valid Until</label><input type="date" value="${new Date(Date.now() + 14 * 86400000).toISOString().slice(0,10)}">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Salesperson</label><input type="text" placeholder="Enter salesperson name">
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Demo quote converted to sales order.'); closeCenterModal();">Convert to Sales Order</button><button class="btn btn-sm" onclick="showToast('Demo quote saved successfully.'); closeCenterModal();">Save Quote</button>`;
  openDemoPanel('New Quote', html, actions);
}

function openNewPaymentDemo() {
  const html = `
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Customer Name</label><input type="text" placeholder="Enter customer name">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Sales Order No.</label><input type="text" placeholder="e.g. SO-2026-0123">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Payment Method</label>
      <select><option>Cash</option><option>Bank Transfer</option><option>Check</option><option>Credit Card</option></select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Reference Number</label><input type="text" placeholder="e.g. TXN-99812">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Amount Paid</label><input type="number" value="0">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Payment Date</label><input type="date" value="${new Date().toISOString().slice(0,10)}">
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Demo payment recorded successfully.'); closeCenterModal();">Save Payment</button><button class="btn btn-sm" onclick="window.print()">Print Receipt</button>`;
  openDemoPanel('New Payment', html, actions);
}

function openNewCustomerDemo() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Full Name</label><input type="text" placeholder="Enter customer name">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Contact Number</label><input type="text" placeholder="e.g. 0917 123 4567">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Email Address</label><input type="email" placeholder="customer@example.com">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Address</label><textarea rows="2" style="width:100%; border:1px solid var(--border-strong); padding:5px; font-family:inherit;"></textarea>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Preferred Branch</label><select>${branches}</select>
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Customer Type</label><select><option>Retail</option><option>Corporate</option><option>Fleet</option></select>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Demo customer saved successfully.'); closeCenterModal();">Save Customer</button>`;
  openDemoPanel('New Customer', html, actions);
}

function openInvoicesDemo() {
  const html = `
    <div class="sp-section-label">Recent Invoices</div>
    <div class="table-wrap" style="border:1px solid var(--border); margin-top:12px;">
      <table>
        <thead><tr><th>Invoice No.</th><th>Sales Order No.</th><th>Customer</th><th>Invoice Date</th><th>Due Date</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td class="mono">INV-2026-001</td><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td>May 10, 2026</td><td>May 25, 2026</td><td class="amt">₱75,000</td><td>${badge('Paid')}</td></tr>
          <tr><td class="mono">INV-2026-002</td><td class="mono">SO-2026-0102</td><td>Patrick Lim</td><td>May 12, 2026</td><td>May 27, 2026</td><td class="amt">₱140,000</td><td>${badge('Pending')}</td></tr>
          <tr><td class="mono">INV-2026-003</td><td class="mono">SO-2026-0098</td><td>Jessa Cruz</td><td>May 01, 2026</td><td>May 15, 2026</td><td class="amt">₱85,000</td><td>${badge('Overdue')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Demo invoice opened.')">View Invoice</button><button class="btn btn-sm" onclick="window.print()">Print Invoice</button><button class="btn btn-sm" onclick="exportCsv('sales')">Export CSV</button>`;
  openDemoPanel('Invoices List', html, actions);
}

function openShipmentsDemo() {
  const html = `
    <div class="sp-section-label">Tracking Summary</div>
    <div class="table-wrap" style="border:1px solid var(--border); margin-top:12px;">
      <table>
        <thead><tr><th>Shipment No.</th><th>Sales Order No.</th><th>Customer</th><th>Unit Model</th><th>Branch</th><th>Release Date</th><th>Delivery Status</th></tr></thead>
        <tbody>
          <tr><td class="mono">SHP-2026-051</td><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td>Honda ADV 160</td><td>Manila Main</td><td>May 14, 2026</td><td>${badge('Processing')}</td></tr>
          <tr><td class="mono">SHP-2026-052</td><td class="mono">SO-2026-0102</td><td>Patrick Lim</td><td>Yamaha NMAX</td><td>Quezon City North</td><td>May 15, 2026</td><td>${badge('Pending')}</td></tr>
          <tr><td class="mono">SHP-2026-048</td><td class="mono">SO-2026-0098</td><td>Jessa Cruz</td><td>Suzuki Raider R150</td><td>Cebu Branch</td><td>May 12, 2026</td><td>${badge('Delivered')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Shipment marked as delivered.')">Mark as Delivered</button><button class="btn btn-sm" onclick="showToast('Demo shipment opened.')">View Shipment</button><button class="btn btn-sm" onclick="window.print()">Print Delivery Slip</button>`;
  openDemoPanel('Shipment Tracking', html, actions);
}

function openSalesPricesDemo() {
  const html = `
    <div class="sp-section-label">Price List Configuration</div>
    <div class="table-wrap" style="border:1px solid var(--border); margin-top:12px;">
      <table>
        <thead><tr><th>Unit Model</th><th>SRP</th><th>Cash Discount</th><th>Installment Price</th><th>Effective Date</th></tr></thead>
        <tbody>
          <tr><td>Honda ADV 160</td><td class="amt">₱166,900</td><td class="amt">₱5,000</td><td class="amt">₱185,000</td><td>Jan 1, 2026</td></tr>
          <tr><td>Honda Click 125i</td><td class="amt">₱82,900</td><td class="amt">₱2,500</td><td class="amt">₱95,000</td><td>Jan 1, 2026</td></tr>
          <tr><td>Yamaha NMAX</td><td class="amt">₱151,900</td><td class="amt">₱4,000</td><td class="amt">₱170,000</td><td>Mar 15, 2026</td></tr>
          <tr><td>Suzuki Raider R150</td><td class="amt">₱115,900</td><td class="amt">₱3,000</td><td class="amt">₱130,000</td><td>Feb 10, 2026</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Demo price editor opened.')">Edit Price</button><button class="btn btn-sm" onclick="exportCsv('sales')">Export CSV</button>`;
  openDemoPanel('Sales Prices', html, actions);
}

function openProcessDemo(action) {
  if (action === 'Process Orders') return openProcessOrdersProcess();
  if (action === 'Generate Intercompany Sales Orders') return openIntercompanySalesOrdersProcess();
  if (action === 'Process Shipments') return openShipmentsProcess();
  if (action === 'Process Invoices and Memos') return openInvoicesAndMemosProcess();
  if (action === 'Create Transfer Orders') return openTransferOrdersProcess();
  if (action === 'Print/Email Orders') return openPrintEmailOrdersProcess();

  return openGenericProcessDemo(action);
}

function openProcessOrdersProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Branch Filter</label><select><option>All Branches</option>${branches}</select></div>
      <div class="orcr-field"><label>Order Date From</label><input type="date" value="${new Date(new Date().setDate(1)).toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Order Date To</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Payment Mode</label><select><option>All Modes</option><option>Cash</option><option>Installment</option><option>Bank Financing</option></select></div>
      <div class="orcr-field"><label>Order Status</label><select><option>Pending</option><option>Processing</option><option>Completed</option></select></div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Sales Order No.</th><th>Customer</th><th>Unit Model</th><th>Payment Mode</th><th>Branch</th><th>Order Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td>Honda ADV 160</td><td>Cash</td><td>Manila Main</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td class="mono">SO-2026-0102</td><td>Patrick Lim</td><td>Yamaha NMAX</td><td>Installment</td><td>Quezon City North</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateSpecificProcess(this, 'Processed', 'Demo sales orders processed successfully.')">Process Selected Orders</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: Orders validated.')">Validate Orders</button>
    <button class="btn btn-sm" onclick="printProcessLog()">Print Order Processing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Process Orders', html, actions);
}

function openIntercompanySalesOrdersProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Source Branch</label><select>${branches}</select></div>
      <div class="orcr-field"><label>Destination Branch</label><select>${branches}</select></div>
      <div class="orcr-field"><label>Transfer Reason</label><input type="text" value="Stock Replenishment"></div>
      <div class="orcr-field"><label>Transaction Date</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
      <div class="orcr-field" style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" id="icso-avail" checked style="width:auto; margin:0;">
        <label for="icso-avail" style="margin:0;">Include Available Units</label>
      </div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Source Branch</th><th>Destination Branch</th><th>Unit Model</th><th>Quantity</th><th>Intercompany Ref No.</th><th>Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td>Manila Main</td><td>Quezon City North</td><td>Honda Click 125i</td><td>5</td><td class="mono">IC-2026-001</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td>Manila Main</td><td>Makati Ayala</td><td>Yamaha Mio Gear</td><td>3</td><td class="mono">IC-2026-002</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateSpecificProcess(this, 'Generated', 'Demo intercompany sales orders generated.')">Generate Intercompany Orders</button>
    <button class="btn btn-sm" onclick="exportCsv('sales')">Export Intercompany Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Generate Intercompany Sales Orders', html, actions);
}

function openShipmentsProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Branch</label><select><option>All Branches</option>${branches}</select></div>
      <div class="orcr-field"><label>Release Date</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Delivery Status</label><select><option>Pending</option><option>In Transit</option><option>Delivered</option></select></div>
      <div class="orcr-field"><label>OR/CR Validation</label><select><option>Required</option><option>Optional</option></select></div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Shipment No.</th><th>Sales Order No.</th><th>Customer</th><th>Unit Model</th><th>OR No.</th><th>CR No.</th><th>Release Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td class="mono">SHP-2026-01</td><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td>Honda ADV 160</td><td class="mono">OR-1234</td><td class="mono">CR-5678</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td class="mono">SHP-2026-02</td><td class="mono">SO-2026-0102</td><td>Patrick Lim</td><td>Yamaha NMAX</td><td class="mono">OR-1235</td><td class="mono">CR-5679</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateSpecificProcess(this, 'Ready for Release', 'Demo shipments processed successfully.')">Process Shipments</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: OR/CR validated successfully.')">Validate OR/CR</button>
    <button class="btn btn-sm" onclick="window.print()">Print Delivery Slip</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Process Shipments', html, actions);
}

function openInvoicesAndMemosProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Invoice Date</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Customer Type</label><select><option>All Types</option><option>Retail</option><option>Corporate</option><option>Fleet</option></select></div>
      <div class="orcr-field"><label>Branch</label><select><option>All Branches</option>${branches}</select></div>
      <div class="orcr-field" style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" id="iam-cm" checked style="width:auto; margin:0;">
        <label for="iam-cm" style="margin:0;">Include Credit Memo</label>
      </div>
      <div class="orcr-field" style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" id="iam-dm" checked style="width:auto; margin:0;">
        <label for="iam-dm" style="margin:0;">Include Debit Memo</label>
      </div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Invoice No.</th><th>Sales Order No.</th><th>Customer</th><th>Invoice Amount</th><th>Memo Type</th><th>Invoice Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td class="mono">INV-2026-001</td><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td class="amt">₱75,000</td><td>None</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td class="mono">INV-2026-002</td><td class="mono">SO-2026-0102</td><td>Patrick Lim</td><td class="amt">₱140,000</td><td>Credit</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateSpecificProcess(this, 'Generated', 'Demo invoices generated successfully.')">Generate Invoices</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: Memos applied successfully.')">Apply Memos</button>
    <button class="btn btn-sm" onclick="window.print()">Print Invoice Batch</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Process Invoices and Memos', html, actions);
}

function openTransferOrdersProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>From Branch</label><select id="to-from-branch">${branches}</select></div>
      <div class="orcr-field"><label>To Branch</label><select id="to-to-branch">${branches}</select></div>
      <div class="orcr-field"><label>Unit Model</label><input type="text" id="to-model" placeholder="e.g. Honda Click 125i" value="Honda Click 125i"></div>
      <div class="orcr-field"><label>Quantity</label><input type="number" id="to-qty" value="1"></div>
      <div class="orcr-field"><label>Transfer Reason</label><input type="text" id="to-reason" value="Inventory Balancing"></div>
      <div class="orcr-field"><label>Requested By</label><input type="text" id="to-requested" value="Admin User"></div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Transfer Order No.</th><th>From Branch</th><th>To Branch</th><th>Unit Model</th><th>Quantity</th><th>Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td class="mono">TR-2026-001</td><td>Manila Main</td><td>Quezon City North</td><td>Yamaha NMAX</td><td>2</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateCreateTransferOrder(this)">Create Transfer Order</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: Transfer order draft saved.')">Save Draft</button>
    <button class="btn btn-sm" onclick="window.print()">Print Transfer Request</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Create Transfer Orders', html, actions);
}

function openPrintEmailOrdersProcess() {
  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Sales Order</label><select><option>SO-2026-0101 (Maria Santos)</option><option>SO-2026-0102 (Patrick Lim)</option></select></div>
      <div class="orcr-field"><label>Document Type</label><select><option>Sales Order Form</option><option>Proforma Invoice</option><option>Delivery Receipt</option></select></div>
      <div class="orcr-field"><label>Recipient Email</label><input type="email" value="customer@example.com"></div>
      <div class="orcr-field" style="grid-column: span 3;">
        <label>Message</label>
        <textarea rows="3" style="width:100%; border:1px solid var(--border-strong); padding:5px; font-family:inherit;">Attached is your requested sales order document.</textarea>
      </div>
      <div class="orcr-field" style="display:flex; align-items:center; gap:8px;">
        <input type="checkbox" id="peo-attach" checked style="width:auto; margin:0;">
        <label for="peo-attach" style="margin:0;">Include Attachments</label>
      </div>
    </div>
    <div class="sp-section-label">Affected Records</div>
    <div class="table-wrap" style="border:1px solid var(--border);">
      <table>
        <thead><tr><th>Sales Order No.</th><th>Customer</th><th>Document Type</th><th>Recipient</th><th>Delivery Method</th><th>Status</th></tr></thead>
        <tbody id="demo-process-records">
          <tr><td class="mono">SO-2026-0101</td><td>Maria Santos</td><td>Sales Order Form</td><td>customer@example.com</td><td>Email</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateSpecificProcess(this, 'Email Prepared', 'Demo only: sales order email prepared successfully.')">Send Demo Email</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: Email prepared and saved to drafts.')">Prepare Email</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel('Print/Email Orders', html, actions);
}

function openGenericProcessDemo(action) {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  let checklist = [];
  
  checklist = ['Initialize process', 'Validate records', 'Execute update', 'Finish'];

  const html = `
    <div class="orcr-form-grid" style="margin-bottom: 16px;">
      <div class="orcr-field"><label>Branch</label><select><option>All Branches</option>${branches}</select></div>
      <div class="orcr-field"><label>Date From</label><input type="date" value="${new Date(new Date().setDate(1)).toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Date To</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
      <div class="orcr-field"><label>Status</label><select><option>Pending</option><option>Processing</option><option>Completed</option><option>All</option></select></div>
    </div>
    <div class="sp-row" style="margin-bottom: 16px; background: var(--surface); padding: 10px 14px; border: 1px solid var(--border); border-radius: 4px;">
      <span class="sp-key" style="font-size:13px;">Process Name: <strong style="color:var(--text-primary)">${esc(action)}</strong></span>
      <span class="sp-val" style="font-size:13px;">Status: <span id="demo-process-status">${badge('Ready')}</span></span>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div>
        <div class="sp-section-label">Checklist</div>
        <ul style="padding-left:20px; font-size:12.5px; color:var(--text-secondary); line-height:1.6;">
          ${checklist.map(item => `<li>${item}</li>`).join('')}
        </ul>
      </div>
      <div>
        <div class="sp-section-label">Affected Records</div>
        <div class="table-wrap" style="border:1px solid var(--border);">
          <table>
            <thead><tr><th>Record ID</th><th>Description</th><th>Status</th></tr></thead>
            <tbody id="demo-process-records">
              <tr><td class="mono">REC-2026-01</td><td>Batch item 1</td><td class="rec-status">${badge('Pending')}</td></tr>
              <tr><td class="mono">REC-2026-02</td><td>Batch item 2</td><td class="rec-status">${badge('Pending')}</td></tr>
              <tr><td class="mono">REC-2026-03</td><td>Batch item 3</td><td class="rec-status">${badge('Pending')}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateProcessStart(this)">Start Process</button>
    <button class="btn btn-sm" onclick="simulateProcessComplete(this)">Complete Process</button>
    <button class="btn btn-sm" onclick="printProcessLog()">Print Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openDemoPanel(`Process: ${action}`, html, actions);
}

function simulateSpecificProcess(btn, targetStatus, message) {
  flashButton(btn, 'Processing...');
  const recStatuses = document.querySelectorAll('#demo-process-records .rec-status');
  recStatuses.forEach(el => el.innerHTML = badge(targetStatus));
  showToast(message);
}

function simulateCreateTransferOrder(btn) {
  flashButton(btn, 'Creating...');
  const tbody = document.getElementById('demo-process-records');
  const fromBranch = document.getElementById('to-from-branch')?.value || 'Branch A';
  const toBranch = document.getElementById('to-to-branch')?.value || 'Branch B';
  const model = document.getElementById('to-model')?.value || 'Unit Model';
  const qty = document.getElementById('to-qty')?.value || '1';
  
  if (tbody) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class="mono">TR-2026-002</td><td>${esc(fromBranch)}</td><td>${esc(toBranch)}</td><td>${esc(model)}</td><td>${esc(qty)}</td><td class="rec-status">${badge('Created')}</td>`;
    tbody.appendChild(tr);
  }
  
  showToast('Demo transfer order created successfully.');
}

function simulateProcessStart(btn) {
  flashButton(btn, 'Processing...');
  const statusEl = document.getElementById('demo-process-status');
  if (statusEl) statusEl.innerHTML = badge('Processing');
  showToast('Demo process started.');
}

function simulateProcessComplete(btn) {
  flashButton(btn, 'Completing...');
  const statusEl = document.getElementById('demo-process-status');
  if (statusEl) statusEl.innerHTML = badge('Completed');
  const recStatuses = document.querySelectorAll('#demo-process-records .rec-status');
  recStatuses.forEach(el => el.innerHTML = badge('Completed'));
  showToast('Demo process completed successfully.');
}

function printProcessLog() {
  showToast('Demo process log prepared for printing.');
  setTimeout(() => {
    window.print();
  }, 500);
}

// ===== RECEIVABLES HELPERS =====
function demoToday() {
  return new Date().toISOString().slice(0, 10);
}
function demoRef(prefix, num) {
  return `${prefix}-2026-${String(num).padStart(4, '0')}`;
}
function setButtonLoading(btn, text) {
  if (!btn) return;
  btn._origText = btn.textContent;
  btn.textContent = text || 'Processing...';
  btn.classList.add('busy');
  btn.disabled = true;
}
function resetButtonLoading(btn) {
  if (!btn) return;
  btn.textContent = btn._origText || 'Done';
  btn.classList.remove('busy');
  btn.disabled = false;
}
function exportRowsToCsv(filename, rows) {
  if (!rows || !rows.length) { showToast('No data to export.'); return; }
  const headers = Object.keys(rows[0]);
  const csv = [headers.join(','), ...rows.map(r => headers.map(h => csvEscape(r[h])).join(','))].join('\n');
  downloadFile(filename, csv, 'text/csv;charset=utf-8');
  showToast(`${filename} exported with ${rows.length} records.`);
}

// ===== RECEIVABLES DEMO DATA & LOGIC =====
let demoReceivablesData = JSON.parse(localStorage.getItem('demoReceivablesData')) || {
  invoices: [
    { no: 'INV-2026-1001', soNo: 'SO-2026-0501', customer: 'Maria Santos', customerId: 'CUST-001', date: '2026-05-01', due: '2026-05-15', amount: 45000, balance: 45000, memoType: 'None', status: 'Open', printStatus: 'Pending', eligible: 'Yes' },
    { no: 'INV-2026-1002', soNo: 'SO-2026-0505', customer: 'Juan Reyes', customerId: 'CUST-002', date: '2026-05-03', due: '2026-05-18', amount: 120000, balance: 0, memoType: 'None', status: 'Paid', printStatus: 'Printed', eligible: 'No' },
    { no: 'INV-2026-1003', soNo: 'SO-2026-0420', customer: 'Patrick Lim', customerId: 'CUST-003', date: '2026-04-20', due: '2026-05-05', amount: 3500, balance: 3500, memoType: 'None', status: 'Overdue', printStatus: 'Pending', eligible: 'Yes' },
    { no: 'INV-2026-1004', soNo: 'SO-2026-0510', customer: 'Carlos Reyes', customerId: 'CUST-004', date: '2026-05-05', due: '2026-05-20', amount: 82900, balance: 42900, memoType: 'None', status: 'Partial', printStatus: 'Printed', eligible: 'No' },
    { no: 'INV-2026-1005', soNo: 'SO-2026-0515', customer: 'Angela Dizon', customerId: 'CUST-005', date: '2026-05-07', due: '2026-05-22', amount: 166900, balance: 0, memoType: 'None', status: 'Paid', printStatus: 'Printed', eligible: 'No' },
    { no: 'INV-2026-1006', soNo: 'SO-2026-0518', customer: 'Carlos Reyes', customerId: 'CUST-004', date: '2026-05-10', due: '2026-05-25', amount: 25000, balance: 25000, memoType: 'None', status: 'Open', printStatus: 'Pending', eligible: 'Yes' },
    { no: 'INV-2026-1007', soNo: 'SO-2026-0520', customer: 'Angela Dizon', customerId: 'CUST-005', date: '2026-05-12', due: '2026-05-27', amount: 15500, balance: 15500, memoType: 'None', status: 'Open', printStatus: 'Pending', eligible: 'Yes' },
    { no: 'INV-2026-1008', soNo: 'SO-2026-0498', customer: 'Maria Santos', customerId: 'CUST-001', date: '2026-04-25', due: '2026-05-10', amount: 22000, balance: 0, memoType: 'None', status: 'Paid', printStatus: 'Printed', eligible: 'No' }
  ],
  payments: [
    { no: 'PAY-2026-8001', customer: 'Juan Reyes', customerId: 'CUST-002', invNo: 'INV-2026-1002', date: '2026-05-10', method: 'Bank Transfer', ref: 'TXN-99123', amount: 120000, status: 'Applied' },
    { no: 'PAY-2026-8002', customer: 'Carlos Reyes', customerId: 'CUST-004', invNo: 'INV-2026-1004', date: '2026-05-12', method: 'Cash', ref: 'REC-44210', amount: 40000, status: 'Applied' },
    { no: 'PAY-2026-8003', customer: 'Angela Dizon', customerId: 'CUST-005', invNo: 'INV-2026-1005', date: '2026-05-14', method: 'Check', ref: 'CHK-10045', amount: 166900, status: 'Applied' },
    { no: 'PAY-2026-8004', customer: 'Maria Santos', customerId: 'CUST-001', invNo: 'INV-2026-1008', date: '2026-05-08', method: 'Bank Transfer', ref: 'TXN-99180', amount: 22000, status: 'Applied' }
  ],
  customers: [
    { id: 'CUST-001', name: 'Maria Santos', contact: '0917 123 4567', email: 'maria@example.com', branch: 'Manila Main', type: 'Retail', creditTerm: 'NET15', balance: 45000, status: 'Active', createdDate: '2025-11-15', notes: 'Loyal customer since 2025. Prefers bank transfer payments.' },
    { id: 'CUST-002', name: 'Juan Reyes', contact: '0918 987 6543', email: 'juan@example.com', branch: 'Makati Ayala', type: 'Retail', creditTerm: 'NET30', balance: 0, status: 'Active', createdDate: '2025-12-01', notes: 'Fully paid customer. Referred by Maria Santos.' },
    { id: 'CUST-003', name: 'Patrick Lim', contact: '0919 444 5555', email: 'patrick@example.com', branch: 'Quezon City North', type: 'Corporate', creditTerm: '2/10N30', balance: 3500, status: 'Overdue', createdDate: '2026-01-10', notes: 'Corporate fleet account. Overdue on INV-2026-1003.' },
    { id: 'CUST-004', name: 'Carlos Reyes', contact: '0920 888 9999', email: 'carlos.reyes@example.com', branch: 'Cebu Main', type: 'Retail', creditTerm: 'NET30', balance: 67900, status: 'Partial', createdDate: '2026-02-20', notes: 'Partial payment made on Honda Click 125i. Additional open invoice for accessories.' },
    { id: 'CUST-005', name: 'Angela Dizon', contact: '0921 222 3333', email: 'angela.dizon@example.com', branch: 'Davao South', type: 'Corporate', creditTerm: 'NET15', balance: 15500, status: 'Active', createdDate: '2026-03-05', notes: 'Corporate account. ADV 160 fully paid. Open balance on accessories invoice.' }
  ],
  creditTerms: [
    { code: 'NET15', desc: 'Net 15 Days', days: 15, discount: '0%', status: 'Active' },
    { code: 'NET30', desc: 'Net 30 Days', days: 30, discount: '0%', status: 'Active' },
    { code: '2/10N30', desc: '2% 10 Days, Net 30', days: 30, discount: '2%', status: 'Active' }
  ],
  salesPrices: [
    { model: 'Honda Click 125i', srp: 82900, cashDisc: 2000, instPrice: 95000, date: '2026-01-01' },
    { model: 'Yamaha NMAX', srp: 151900, cashDisc: 3000, instPrice: 170000, date: '2026-02-15' },
    { model: 'Honda ADV 160', srp: 166900, cashDisc: 5000, instPrice: 185000, date: '2026-01-01' }
  ]
};
function saveDemoReceivablesData() {
  localStorage.setItem('demoReceivablesData', JSON.stringify(demoReceivablesData));
}

function openReceivablesDemo(action, type) {
  if (action === 'New Invoice') return openArNewInvoiceDemo();
  if (action === 'New Payment') return openArNewPaymentDemo();
  if (action === 'New Customer') return openArNewCustomerDemo();
  if (action === 'Customer Details' && type === 'ar-shortcut') return openArCustomersDemo();

  if (action === 'Invoices and Memos') return openArInvoicesDemo();
  if (action === 'Payments and Applications') return openArPaymentsDemo();
  if (action === 'Customers' || action === 'Customer Details') return openArCustomersDemo();
  if (action === 'Credit Terms') return openArCreditTermsDemo();
  if (action === 'Sales Prices') return openArSalesPricesDemo();

  if (action === 'Release AR Documents') return openArReleaseDocsProcess();
  if (action === 'Print Invoices and Memos') return openArPrintInvoicesProcess();
  if (action === 'Write Off Balances and Credits') return openArWriteOffProcess();
  if (action === 'Prepare Statements') return openArPrepareStatementsProcess();
  if (action === 'Print Statements') return openArPrintStatementsProcess();
  if (action === 'Close Financial Periods') return openArClosePeriodsProcess();

  if (action === 'Customer Summary') return openArCustomerSummaryDemo();
  if (action === 'Statement History Summary') return openArStatementHistoryDemo();
  if (type === 'ar-printed-form') return openArPrintedFormDemo(action);
  if (type === 'ar-report') return openArReportDemo(action);
  if (type === 'ar-profitability') return openArProfitabilityDemo(action);

  return openGenericProcessDemo(action);
}

function openArNewInvoiceDemo() {
  const customers = demoReceivablesData.customers.map(c => `<option value="${esc(c.name)}">${esc(c.name)}</option>`).join('');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Customer</label><select id="ar-inv-cust">${customers}</select></div>
      <div class="demo-form-row"><label>Sales Order No.</label><input type="text" id="ar-inv-so" placeholder="e.g. SO-2026-0599"></div>
      <div class="demo-form-row"><label>Invoice Date</label><input type="date" id="ar-inv-date" value="${demoToday()}"></div>
      <div class="demo-form-row"><label>Due Date</label><input type="date" id="ar-inv-due" value="${new Date(Date.now() + 15 * 86400000).toISOString().slice(0,10)}"></div>
      <div class="demo-form-row"><label>Invoice Amount</label><input type="number" id="ar-inv-amt" value="0"></div>
      <div class="demo-form-row"><label>Memo Type</label><select id="ar-inv-memo"><option>None</option><option>Debit Memo</option><option>Credit Memo</option></select></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="createArInvoice(this)">Create Invoice</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: Invoice draft saved.'); closeCenterModal();">Save Draft</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('New Invoice', html, actions);
}

function createArInvoice(btn) {
  const cust = document.getElementById('ar-inv-cust').value;
  const so = document.getElementById('ar-inv-so').value || 'Manual';
  const date = document.getElementById('ar-inv-date').value;
  const due = document.getElementById('ar-inv-due').value;
  const amt = Number(document.getElementById('ar-inv-amt').value) || 0;
  const memo = document.getElementById('ar-inv-memo').value;

  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const invNo = demoRef('INV', demoReceivablesData.invoices.length + 1000);
    demoReceivablesData.invoices.unshift({ no: invNo, soNo: so, customer: cust, date, due, amount: amt, balance: amt, memoType: memo, status: 'Draft', printStatus: 'Pending', eligible: 'No' });
    
    const c = demoReceivablesData.customers.find(x => x.name === cust);
    if (c) c.balance += amt;

    saveDemoReceivablesData();
    resetButtonLoading(btn);
    showToast('Demo invoice created successfully.');
    closeCenterModal();
  }, 600);
}

function openArNewPaymentDemo() {
  const invoices = demoReceivablesData.invoices.filter(i => i.balance > 0).map(i => `<option value="${esc(i.no)}">${esc(i.no)} - ₱${fmt(i.balance)}</option>`).join('');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Invoice</label><select id="ar-pay-inv">${invoices}</select></div>
      <div class="demo-form-row"><label>Customer Name</label><input type="text" id="ar-pay-cust" placeholder="Auto-filled"></div>
      <div class="demo-form-row"><label>Payment Date</label><input type="date" id="ar-pay-date" value="${demoToday()}"></div>
      <div class="demo-form-row"><label>Payment Method</label><select id="ar-pay-method"><option>Cash</option><option>Bank Transfer</option><option>Check</option><option>Credit Card</option></select></div>
      <div class="demo-form-row"><label>Reference Number</label><input type="text" id="ar-pay-ref" placeholder="e.g. TXN-12345"></div>
      <div class="demo-form-row"><label>Amount Paid</label><input type="number" id="ar-pay-amt" value="0"></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="createArPayment(this)">Save Payment</button>
    <button class="btn btn-sm" onclick="window.print()">Print Receipt</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('New Payment', html, actions);
  
  document.getElementById('ar-pay-inv')?.addEventListener('change', (e) => {
    const inv = demoReceivablesData.invoices.find(i => i.no === e.target.value);
    if (inv) {
      document.getElementById('ar-pay-cust').value = inv.customer;
      document.getElementById('ar-pay-amt').value = inv.balance;
    }
  });
}

function createArPayment(btn) {
  const invNo = document.getElementById('ar-pay-inv').value;
  const cust = document.getElementById('ar-pay-cust').value;
  const date = document.getElementById('ar-pay-date').value;
  const method = document.getElementById('ar-pay-method').value;
  const ref = document.getElementById('ar-pay-ref').value;
  const amt = Number(document.getElementById('ar-pay-amt').value) || 0;

  if (!invNo || amt <= 0) { showToast('Please select an invoice and enter an amount.'); return; }

  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const payNo = demoRef('PAY', demoReceivablesData.payments.length + 8000);
    demoReceivablesData.payments.unshift({ no: payNo, customer: cust, invNo, date, method, ref, amount: amt, status: 'Applied' });
    
    const inv = demoReceivablesData.invoices.find(i => i.no === invNo);
    if (inv) {
      inv.balance = Math.max(0, inv.balance - amt);
      if (inv.balance === 0) inv.status = 'Paid';
    }
    const c = demoReceivablesData.customers.find(x => x.name === cust);
    if (c) c.balance = Math.max(0, c.balance - amt);

    saveDemoReceivablesData();
    resetButtonLoading(btn);
    showToast('Demo payment recorded successfully.');
    closeCenterModal();
  }, 600);
}

function openArCustomersDemo() {
  const html = `
    <div id="ar-customer-records-wrap">
      ${renderCustomerRecords()}
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('customers.csv', demoReceivablesData.customers)">Export All CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Customer Records', html, actions, { width: 'min(1040px, calc(100vw - 48px))' });
}

function renderCustomerRecords() {
  return `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Customer ID</th><th>Name</th><th>Contact</th><th>Email</th><th>Branch</th><th>Balance</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
        <tbody>
          ${demoReceivablesData.customers.map(c => `<tr style="cursor:pointer" onclick="openCustomerProfile('${esc(c.id)}')" title="Click to view profile">
            <td class="mono">${esc(c.id)}</td><td><strong>${esc(c.name)}</strong></td><td class="mono dim">${esc(c.contact)}</td>
            <td class="dim">${esc(c.email)}</td><td>${esc(c.branch)}</td><td class="amt" style="${c.balance>0?'color:var(--red)':'color:var(--green)'}">₱${fmt(c.balance)}</td>
            <td>${badge(c.status)}</td>
            <td style="text-align:center"><button class="btn btn-sm" onclick="event.stopPropagation(); openCustomerProfile('${esc(c.id)}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
}

function getCustomerInvoices(customerId) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) return [];
  return demoReceivablesData.invoices.filter(i => i.customerId === customerId || i.customer === c.name);
}

function getCustomerPayments(customerId) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) return [];
  return demoReceivablesData.payments.filter(p => p.customerId === customerId || p.customer === c.name);
}

function openCustomerProfile(customerId) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) { showToast('Customer not found.'); return; }

  const invoices = getCustomerInvoices(customerId);
  const payments = getCustomerPayments(customerId);
  const totalInvoiced = invoices.reduce((s, i) => s + i.amount, 0);
  const totalPaid = payments.reduce((s, p) => s + p.amount, 0);
  const remainingBalance = c.balance;
  const lastPayment = payments.length ? payments.sort((a, b) => b.date.localeCompare(a.date))[0] : null;
  const creditTermObj = demoReceivablesData.creditTerms.find(t => t.code === (c.creditTerm || 'NET30'));
  const creditTermLabel = creditTermObj ? creditTermObj.desc : (c.creditTerm || 'Net 30 Days');

  let agingStatus = 'Current';
  if (c.status === 'Overdue') agingStatus = 'Overdue';
  else if (c.balance > 0 && invoices.some(i => i.status === 'Overdue')) agingStatus = 'Overdue';
  else if (c.balance > 0) agingStatus = 'Outstanding';

  const html = `
    <div class="sp-section">
      <div class="sp-section-label">Basic Information</div>
      <div class="sp-row"><span class="sp-key">Customer ID</span><span class="sp-val mono">${esc(c.id)}</span></div>
      <div class="sp-row"><span class="sp-key">Full Name</span><span class="sp-val">${esc(c.name)}</span></div>
      <div class="sp-row"><span class="sp-key">Contact Number</span><span class="sp-val mono">${esc(c.contact)}</span></div>
      <div class="sp-row"><span class="sp-key">Email Address</span><span class="sp-val">${esc(c.email)}</span></div>
      <div class="sp-row"><span class="sp-key">Branch</span><span class="sp-val">${esc(c.branch)}</span></div>
      <div class="sp-row"><span class="sp-key">Customer Type</span><span class="sp-val">${esc(c.type || 'Retail')}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(c.status)}</span></div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Account Summary</div>
      <div class="sp-row"><span class="sp-key">Total Invoice Amount</span><span class="sp-val mono">₱${fmt(totalInvoiced)}</span></div>
      <div class="sp-row"><span class="sp-key">Total Paid</span><span class="sp-val mono" style="color:var(--green)">₱${fmt(totalPaid)}</span></div>
      <div class="sp-row"><span class="sp-key">Remaining Balance</span><span class="sp-val mono" style="${remainingBalance>0?'color:var(--red)':''}">₱${fmt(remainingBalance)}</span></div>
      <div class="sp-row"><span class="sp-key">Credit Term</span><span class="sp-val">${esc(creditTermLabel)}</span></div>
      <div class="sp-row"><span class="sp-key">Last Payment Date</span><span class="sp-val mono">${lastPayment ? lastPayment.date : '—'}</span></div>
      <div class="sp-row"><span class="sp-key">Aging Status</span><span class="sp-val">${badge(agingStatus)}</span></div>
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Related Invoices</div>
      ${invoices.length ? `<div class="table-wrap"><table>
        <thead><tr><th>Invoice No.</th><th>SO No.</th><th>Invoice Date</th><th>Due Date</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
        <tbody>${invoices.map(i => {
          const paid = i.amount - i.balance;
          return `<tr>
            <td class="mono">${esc(i.no)}</td><td class="mono dim">${esc(i.soNo)}</td><td class="dim">${esc(i.date)}</td><td class="dim">${esc(i.due)}</td>
            <td class="amt">₱${fmt(i.amount)}</td><td class="amt" style="color:var(--green)">₱${fmt(paid)}</td>
            <td class="amt" style="${i.balance>0?'color:var(--red)':''}">₱${fmt(i.balance)}</td><td>${badge(i.status)}</td>
          </tr>`;
        }).join('')}</tbody>
      </table></div>` : '<p style="color:var(--text-secondary);font-size:12px;margin:8px 0">No invoices found for this customer.</p>'}
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Payment History</div>
      ${payments.length ? `<div class="table-wrap"><table>
        <thead><tr><th>Payment No.</th><th>Invoice No.</th><th>Payment Date</th><th>Method</th><th>Reference No.</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>${payments.map(p => `<tr>
          <td class="mono">${esc(p.no)}</td><td class="mono dim">${esc(p.invNo)}</td><td class="dim">${esc(p.date)}</td>
          <td>${esc(p.method)}</td><td class="mono">${esc(p.ref)}</td>
          <td class="amt" style="color:var(--green)">₱${fmt(p.amount)}</td><td>${badge(p.status)}</td>
        </tr>`).join('')}</tbody>
      </table></div>` : '<p style="color:var(--text-secondary);font-size:12px;margin:8px 0">No payments found for this customer.</p>'}
    </div>

    <div class="sp-section">
      <div class="sp-section-label">Notes / Activity Log</div>
      <div style="border-left:2px solid var(--accent);padding-left:14px;margin-top:6px">
        ${c.notes ? `<div style="font-size:12px;color:var(--text-secondary);margin-bottom:10px;font-style:italic">${esc(c.notes)}</div>` : ''}
        <div style="display:flex;flex-direction:column;gap:8px">
          <div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--accent);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Customer profile created</span><span style="font-size:10px;color:var(--text-tertiary);margin-left:8px">${esc(c.createdDate || '2025-11-15')}</span></div>
          </div>
          ${invoices.length ? `<div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--green);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Invoice generated — ${esc(invoices[invoices.length-1].no)}</span><span style="font-size:10px;color:var(--text-tertiary);margin-left:8px">${esc(invoices[invoices.length-1].date)}</span></div>
          </div>` : ''}
          ${payments.length ? `<div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--green);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Payment applied — ${esc(payments[payments.length-1].no)}</span><span style="font-size:10px;color:var(--text-tertiary);margin-left:8px">${esc(payments[payments.length-1].date)}</span></div>
          </div>` : ''}
          <div style="display:flex;align-items:flex-start;gap:8px">
            <span style="width:6px;height:6px;min-width:6px;border-radius:50%;background:var(--text-tertiary);margin-top:5px"></span>
            <div><span style="font-size:11px;color:var(--text-primary)">Statement prepared</span><span style="font-size:10px;color:var(--text-tertiary);margin-left:8px">${demoToday()}</span></div>
          </div>
        </div>
      </div>
    </div>
  `;

  const actions = `
    <button class="btn btn-sm btn-primary" onclick="openEditCustomerProfile('${esc(c.id)}')">Edit Demo Profile</button>
    <button class="btn btn-sm" onclick="showToast('Demo customer profile prepared for printing.'); setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm" onclick="exportSingleCustomerProfileCsv('${esc(c.id)}')">Export Customer CSV</button>
    <button class="btn btn-sm" onclick="openArCustomersDemo()">Back to List</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Customer Profile: ' + c.name, html, actions, { width: 'min(920px, calc(100vw - 48px))' });
}

function openEditCustomerProfile(customerId) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) { showToast('Customer not found.'); return; }

  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}" ${b.name===c.branch?'selected':''}>${esc(b.name)}</option>`).join('') : `<option ${c.branch==='Manila Main'?'selected':''}>Manila Main</option><option ${c.branch==='Makati Ayala'?'selected':''}>Makati Ayala</option><option ${c.branch==='Quezon City North'?'selected':''}>Quezon City North</option><option ${c.branch==='Cebu Main'?'selected':''}>Cebu Main</option><option ${c.branch==='Davao South'?'selected':''}>Davao South</option>`;
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Full Name</label><input type="text" id="ecp-name" value="${esc(c.name)}"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="ecp-contact" value="${esc(c.contact)}"></div>
      <div class="demo-form-row"><label>Email Address</label><input type="email" id="ecp-email" value="${esc(c.email)}"></div>
      <div class="demo-form-row"><label>Branch</label><select id="ecp-branch">${branches}</select></div>
      <div class="demo-form-row"><label>Customer Type</label><select id="ecp-type"><option ${c.type==='Retail'?'selected':''}>Retail</option><option ${c.type==='Corporate'?'selected':''}>Corporate</option><option ${c.type==='Fleet'?'selected':''}>Fleet</option></select></div>
      <div class="demo-form-row"><label>Status</label><select id="ecp-status"><option ${c.status==='Active'?'selected':''}>Active</option><option ${c.status==='Overdue'?'selected':''}>Overdue</option><option ${c.status==='Partial'?'selected':''}>Partial</option><option ${c.status==='Paid'?'selected':''}>Paid</option><option ${c.status==='Inactive'?'selected':''}>Inactive</option></select></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="saveCustomerProfileChanges('${esc(c.id)}', this)">Save Changes</button>
    <button class="btn btn-sm" onclick="openCustomerProfile('${esc(c.id)}')">Cancel</button>
  `;
  openCenterModal('Edit Customer: ' + c.name, html, actions);
}

function saveCustomerProfileChanges(customerId, btn) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) { showToast('Customer not found.'); return; }

  const oldName = c.name;
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    c.name = document.getElementById('ecp-name').value || c.name;
    c.contact = document.getElementById('ecp-contact').value || c.contact;
    c.email = document.getElementById('ecp-email').value || c.email;
    c.branch = document.getElementById('ecp-branch').value || c.branch;
    c.type = document.getElementById('ecp-type').value || c.type;
    c.status = document.getElementById('ecp-status').value || c.status;

    // Update linked invoices/payments if name changed
    if (oldName !== c.name) {
      demoReceivablesData.invoices.forEach(i => { if (i.customer === oldName || i.customerId === customerId) { i.customer = c.name; i.customerId = customerId; } });
      demoReceivablesData.payments.forEach(p => { if (p.customer === oldName || p.customerId === customerId) { p.customer = c.name; p.customerId = customerId; } });
    }

    saveDemoReceivablesData();
    resetButtonLoading(btn);
    showToast('Demo customer profile updated successfully.');
    openCustomerProfile(customerId);
  }, 600);
}

function exportSingleCustomerProfileCsv(customerId) {
  const c = demoReceivablesData.customers.find(x => x.id === customerId);
  if (!c) { showToast('Customer not found.'); return; }

  const invoices = getCustomerInvoices(customerId);
  const payments = getCustomerPayments(customerId);
  const totalInvoiced = invoices.reduce((s, i) => s + i.amount, 0);
  const totalPaid = payments.reduce((s, p) => s + p.amount, 0);

  let csv = 'CUSTOMER PROFILE\n';
  csv += 'Customer ID,Name,Contact,Email,Branch,Type,Credit Term,Balance,Status,Created Date\n';
  csv += `${c.id},${csvEscape(c.name)},${csvEscape(c.contact)},${csvEscape(c.email)},${csvEscape(c.branch)},${csvEscape(c.type)},${csvEscape(c.creditTerm||'')},${c.balance},${c.status},${c.createdDate||''}\n`;
  csv += `\nACCOUNT SUMMARY\n`;
  csv += `Total Invoiced,Total Paid,Remaining Balance\n`;
  csv += `${totalInvoiced},${totalPaid},${c.balance}\n`;
  csv += `\nINVOICES\n`;
  csv += 'Invoice No,SO No,Date,Due Date,Amount,Balance,Status\n';
  invoices.forEach(i => { csv += `${i.no},${i.soNo},${i.date},${i.due},${i.amount},${i.balance},${i.status}\n`; });
  csv += `\nPAYMENTS\n`;
  csv += 'Payment No,Invoice No,Date,Method,Reference,Amount,Status\n';
  payments.forEach(p => { csv += `${p.no},${p.invNo},${p.date},${csvEscape(p.method)},${p.ref},${p.amount},${p.status}\n`; });

  downloadFile(`customer-${c.id}.csv`, csv, 'text/csv;charset=utf-8');
  showToast('Customer profile exported successfully.');
}

function openArNewCustomerDemo() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Full Name</label><input type="text" id="ar-nc-name" placeholder="Enter customer name"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="ar-nc-contact" placeholder="e.g. 0917 123 4567"></div>
      <div class="demo-form-row"><label>Email Address</label><input type="email" id="ar-nc-email" placeholder="customer@example.com"></div>
      <div class="demo-form-row"><label>Preferred Branch</label><select id="ar-nc-branch">${branches}</select></div>
      <div class="demo-form-row"><label>Customer Type</label><select id="ar-nc-type"><option>Retail</option><option>Corporate</option><option>Fleet</option></select></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="createArCustomer(this)">Save Customer</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('New Customer', html, actions);
}

function createArCustomer(btn) {
  const name = document.getElementById('ar-nc-name').value || 'Demo Customer';
  const contact = document.getElementById('ar-nc-contact').value;
  const email = document.getElementById('ar-nc-email').value;
  const branch = document.getElementById('ar-nc-branch').value;
  const type = document.getElementById('ar-nc-type').value;

  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const custId = demoRef('CUST', demoReceivablesData.customers.length);
    demoReceivablesData.customers.unshift({ id: custId, name, contact, email, branch, type, balance: 0, status: 'Active' });
    saveDemoReceivablesData();
    resetButtonLoading(btn);
    showToast('Demo customer saved successfully.');
    closeCenterModal();
  }, 600);
}

function openArInvoicesDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Invoice No.</th><th>SO No.</th><th>Customer</th><th>Invoice Date</th><th>Due Date</th><th>Amount</th><th>Balance</th><th>Status</th></tr></thead>
        <tbody id="ar-invoices-body">
          ${demoReceivablesData.invoices.map(i => `<tr>
            <td class="mono">${i.no}</td><td class="mono dim">${i.soNo}</td><td><strong>${i.customer}</strong></td>
            <td class="dim">${i.date}</td><td class="dim">${i.due}</td><td class="amt">₱${fmt(i.amount)}</td>
            <td class="amt" style="${i.balance>0?'color:var(--red)':''}">${i.balance > 0 ? '₱'+fmt(i.balance) : '₱0.00'}</td>
            <td>${badge(i.status)}</td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="window.print()">Print</button><button class="btn btn-sm" onclick="exportRowsToCsv('invoices.csv', demoReceivablesData.invoices)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Invoices and Memos', html, actions, { width: 'min(980px, calc(100vw - 48px))' });
}

function openArPaymentsDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Payment No.</th><th>Customer</th><th>Invoice No.</th><th>Payment Date</th><th>Method</th><th>Reference No.</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody id="ar-payments-body">
          ${demoReceivablesData.payments.map(p => `<tr>
            <td class="mono">${p.no}</td><td><strong>${p.customer}</strong></td><td class="mono dim">${p.invNo}</td>
            <td class="dim">${p.date}</td><td>${p.method}</td><td class="mono">${p.ref}</td>
            <td class="amt" style="color:var(--green)">₱${fmt(p.amount)}</td><td>${badge(p.status)}</td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('payments.csv', demoReceivablesData.payments)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Payments and Applications', html, actions, { width: 'min(980px, calc(100vw - 48px))' });
}

function openArCreditTermsDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Term Code</th><th>Description</th><th>Due Days</th><th>Discount</th><th>Status</th></tr></thead>
        <tbody>
          ${demoReceivablesData.creditTerms.map(t => `<tr>
            <td class="mono"><strong>${t.code}</strong></td><td>${t.desc}</td><td class="mono" style="text-align:center">${t.days}</td>
            <td class="mono" style="text-align:center">${t.discount}</td><td>${badge(t.status)}</td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Credit Terms', html, actions);
}

function openArSalesPricesDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Unit Model</th><th>SRP</th><th>Cash Discount</th><th>Installment Price</th><th>Effective Date</th></tr></thead>
        <tbody>
          ${demoReceivablesData.salesPrices.map(p => `<tr>
            <td><strong>${p.model}</strong></td><td class="amt">₱${fmt(p.srp)}</td><td class="amt" style="color:var(--red)">₱${fmt(p.cashDisc)}</td>
            <td class="amt">₱${fmt(p.instPrice)}</td><td class="dim">${p.date}</td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('sales-prices.csv', demoReceivablesData.salesPrices)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Sales Prices', html, actions, { width: 'min(820px, calc(100vw - 48px))' });
}

// ===== PROCESSES =====
function openArReleaseDocsProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const docs = demoReceivablesData.invoices.filter(i => i.status === 'Draft' || i.status === 'Pending');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option>${branches}</select></div>
      <div class="demo-form-row"><label>Document Status</label><select><option>Draft</option><option>Pending</option></select></div>
      <div class="demo-form-row"><label>Date From</label><input type="date" value="${new Date(new Date().setDate(1)).toISOString().slice(0,10)}"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="${new Date().toISOString().slice(0,10)}"></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Documents to Release</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Document No.</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody id="ar-process-records">
          ${docs.length ? docs.map(d => `<tr><td class="mono">${d.no}</td><td>${d.customer}</td><td class="amt">₱${fmt(d.amount)}</td><td class="rec-status">${badge(d.status)}</td></tr>`).join('') : `<tr><td colspan="4" style="text-align:center" class="dim">No pending documents to release. Create a draft invoice first.</td></tr>`}
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, 'invoices', ['Draft','Pending'], 'Open', 'Documents released successfully.')">Release Documents</button>
    <button class="btn btn-sm" onclick="printProcessLog('Release AR Documents')">Print Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Release AR Documents', html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function openArPrintInvoicesProcess() {
  const docs = demoReceivablesData.invoices.filter(i => i.printStatus === 'Pending');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Invoice Batch</label><input type="text" value="BATCH-${demoToday().replace(/-/g,'')}"></div>
      <div class="demo-form-row"><label>Document Type</label><select><option>Invoices</option><option>Credit Memos</option><option>Debit Memos</option></select></div>
      <div class="demo-form-row"><label>Output Format</label><select><option>PDF Download</option><option>Direct to Printer</option></select></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Documents to Print</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Invoice No.</th><th>Customer</th><th>Amount</th><th>Print Status</th></tr></thead>
        <tbody id="ar-process-records">
          ${docs.length ? docs.map(d => `<tr><td class="mono">${d.no}</td><td>${d.customer}</td><td class="amt">₱${fmt(d.amount)}</td><td class="rec-status">${badge(d.printStatus)}</td></tr>`).join('') : `<tr><td colspan="4" style="text-align:center" class="dim">All documents have been printed.</td></tr>`}
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, 'invoices', ['Pending'], 'Printed', 'Print batch prepared.', 'printStatus')">Prepare Print Batch</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Print Invoices and Memos', html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function openArWriteOffProcess() {
  const docs = demoReceivablesData.invoices.filter(i => i.balance > 0 && i.balance <= 5000 && i.eligible === 'Yes');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Customer</label><select><option>All Customers</option></select></div>
      <div class="demo-form-row"><label>Write Off Limit</label><input type="number" value="5000"></div>
      <div class="demo-form-row"><label>Reason Code</label><select><option>Small Balance</option><option>Bad Debt</option></select></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Eligible Balances</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Customer</th><th>Invoice No.</th><th>Balance</th><th>Eligible</th><th>Status</th></tr></thead>
        <tbody id="ar-process-records">
          ${docs.length ? docs.map(d => `<tr><td><strong>${d.customer}</strong></td><td class="mono">${d.no}</td><td class="amt" style="color:var(--red)">₱${fmt(d.balance)}</td><td>Yes</td><td class="rec-status">${badge('Pending')}</td></tr>`).join('') : `<tr><td colspan="5" style="text-align:center" class="dim">No eligible small balances to write off.</td></tr>`}
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, 'invoices', ['Open', 'Overdue'], 'Written Off', 'Selected balances written off.')">Write Off Selected</button>
    <button class="btn btn-sm" onclick="exportCsv('receivables')">Export Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Write Off Balances and Credits', html, actions, { width: 'min(820px, calc(100vw - 48px))' });
}

function openArPrepareStatementsProcess() {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>Manila Main</option>';
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Statement Date</label><input type="date" value="${demoToday()}"></div>
      <div class="demo-form-row"><label>Customer Type</label><select><option>All Types</option><option>Retail</option><option>Corporate</option></select></div>
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option>${branches}</select></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Customers with Open Balances</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Customer</th><th>Open Invoices</th><th>Balance</th><th>Statement Status</th></tr></thead>
        <tbody id="ar-process-records">
          <tr><td><strong>Maria Santos</strong></td><td class="mono">1</td><td class="amt" style="color:var(--red)">₱45,000.00</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td><strong>Patrick Lim</strong></td><td class="mono">1</td><td class="amt" style="color:var(--red)">₱3,500.00</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, null, null, 'Prepared', 'Customer statements prepared successfully.')">Prepare Statements</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Prepare Statements', html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function openArPrintStatementsProcess() {
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Statement Batch</label><input type="text" value="STMT-${demoToday().replace(/-/g,'')}"></div>
      <div class="demo-form-row"><label>Output Type</label><select><option>Print</option><option>Email</option></select></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Prepared Statements</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Statement No.</th><th>Customer</th><th>Balance</th><th>Print Status</th></tr></thead>
        <tbody id="ar-process-records">
          <tr><td class="mono">STMT-2026-001</td><td><strong>Maria Santos</strong></td><td class="amt">₱45,000.00</td><td class="rec-status">${badge('Pending')}</td></tr>
          <tr><td class="mono">STMT-2026-002</td><td><strong>Patrick Lim</strong></td><td class="amt">₱3,500.00</td><td class="rec-status">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, null, null, 'Printed', 'Statements printed/emailed successfully.')">Print Statements</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: PDF exported.')">Export PDF Demo</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Print Statements', html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function openArClosePeriodsProcess() {
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Financial Period</label><select><option>05-2026</option><option>04-2026</option></select></div>
      <div class="demo-form-row"><label>Module</label><input type="text" value="Receivables (AR)" disabled style="background:var(--surface)"></div>
    </div>
    <div class="sp-section-label" style="margin-top:16px;">Period Closing Status</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Period</th><th>Open Documents</th><th>Validation Status</th><th>Closing Status</th></tr></thead>
        <tbody id="ar-process-records">
          <tr><td class="mono">05-2026</td><td class="mono">2</td><td><span class="badge badge-approved">Valid</span></td><td class="rec-status">${badge('Open')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm" onclick="showToast('Period validation passed. Ready to close.')">Validate Period</button>
    <button class="btn btn-sm btn-primary" onclick="processArAction(this, null, null, 'Closed', 'Financial period closed for Receivables.')">Close Period</button>
    <button class="btn btn-sm" onclick="printProcessLog('Period Closing')">Print Closing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Close Financial Periods', html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function processArAction(btn, arrayName, statusMatchArray, newStatus, successMsg, statusField = 'status') {
  setButtonLoading(btn, 'Processing...');
  setTimeout(() => {
    if (arrayName && demoReceivablesData[arrayName]) {
      demoReceivablesData[arrayName].forEach(r => {
        if (statusMatchArray.includes(r[statusField])) r[statusField] = newStatus;
        if (newStatus === 'Written Off') r.balance = 0;
      });
      saveDemoReceivablesData();
    }
    const recStatuses = document.querySelectorAll('#ar-process-records .rec-status');
    recStatuses.forEach(el => el.innerHTML = badge(newStatus));
    resetButtonLoading(btn);
    showToast(successMsg);
  }, 800);
}

// ===== INQUIRIES & REPORTS =====
function openArCustomerSummaryDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Customer</th><th>Total Invoices</th><th>Total Paid</th><th>Balance</th><th>Status</th></tr></thead>
        <tbody>
          ${demoReceivablesData.customers.map(c => `<tr><td><strong>${c.name}</strong></td><td class="mono">₱${fmt(c.balance + (c.balance===0?120000:0))}</td><td class="amt" style="color:var(--green)">₱${fmt(c.balance===0?120000:0)}</td><td class="amt" style="${c.balance>0?'color:var(--red)':''}">₱${fmt(c.balance)}</td><td>${badge(c.status)}</td></tr>`).join('')}
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="exportCsv('receivables')">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Customer Summary', html, actions, { width: 'min(820px, calc(100vw - 48px))' });
}

function openArStatementHistoryDemo() {
  const html = `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Statement No.</th><th>Customer</th><th>Statement Date</th><th>Balance</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td class="mono">STMT-2026-0401</td><td><strong>Maria Santos</strong></td><td class="dim">Apr 30, 2026</td><td class="amt">₱45,000</td><td>${badge('Prepared')}</td></tr>
          <tr><td class="mono">STMT-2026-0402</td><td><strong>Juan Reyes</strong></td><td class="dim">Apr 30, 2026</td><td class="amt">₱120,000</td><td>${badge('Printed')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Statement History Summary', html, actions, { width: 'min(820px, calc(100vw - 48px))' });
}

function openArPrintedFormDemo(action) {
  const html = `
    <div style="padding:20px; border:1px solid var(--border-strong); background:#fff; text-align:center; min-height: 250px;">
      <h2 style="margin-bottom:20px">NEXII BSM DEMO INC.</h2>
      <h3 style="margin-bottom:10px; color:var(--text-secondary)">INVOICE / MEMO</h3>
      <div style="display:flex; justify-content:space-between; margin-bottom:20px; text-align:left; font-size:12px;">
        <div><strong>To:</strong> Maria Santos<br>Manila Main Branch</div>
        <div><strong>Invoice No:</strong> INV-2026-1001<br><strong>Date:</strong> May 01, 2026<br><strong>Due:</strong> May 15, 2026</div>
      </div>
      <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12px; margin-bottom:20px;">
        <tr style="border-bottom:1px solid #ccc"><th style="padding:8px">Description</th><th style="padding:8px; text-align:right">Amount</th></tr>
        <tr style="border-bottom:1px solid #eee"><td style="padding:8px">Honda Click 125i (SO-2026-0501)</td><td style="padding:8px; text-align:right">₱45,000.00</td></tr>
        <tr><td style="padding:8px; text-align:right"><strong>Total Balance Due</strong></td><td style="padding:8px; text-align:right; font-weight:bold; color:var(--red)">₱45,000.00</td></tr>
      </table>
      <div style="font-size:11px; color:var(--text-tertiary)">This is a system-generated printable document preview.</div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="window.print()">Print Invoice/Memo</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal(action, html, actions, { width: 'min(720px, calc(100vw - 48px))' });
}

function openArReportDemo(action) {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>All Branches</option>';
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Branch Filter</label><select><option>All Branches</option>${branches}</select></div>
      <div class="demo-form-row"><label>Customer Status</label><select><option>All</option><option>Active</option><option>Overdue</option></select></div>
    </div>
    <div id="demo-report-results" style="display:none; margin-top:16px;">
      <div class="sp-section-label">Generated Data - ${action}</div>
      <div class="table-wrap" style="border:1px solid var(--border);">
        <table>
          <thead><tr><th>Reference / Account</th><th>Customer</th><th>Balance</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td class="mono">1100-AR</td><td>Maria Santos</td><td class="amt">₱45,000.00</td><td>${badge('Overdue')}</td></tr>
            <tr><td class="mono">1100-AR</td><td>Patrick Lim</td><td class="amt">₱3,500.00</td><td>${badge('Active')}</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateReportRun(this)">Run Report</button>
    <button class="btn btn-sm" onclick="exportCsv('receivables')">Export CSV</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: report email prepared successfully.')">Email Report</button>
  `;
  openCenterModal(`Report: ${action}`, html, actions, { width: 'min(760px, calc(100vw - 48px))' });
}

function openArProfitabilityDemo(action) {
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Profitability View</label><input type="text" value="${action}" disabled style="background:var(--surface)"></div>
    </div>
    <div id="demo-report-results" style="display:none; margin-top:16px;">
      <div class="sp-section-label">Analysis Result</div>
      <div class="table-wrap" style="border:1px solid var(--border);">
        <table>
          <thead><tr><th>Category / Item</th><th>Gross Sales</th><th>Cost</th><th>Gross Profit</th><th>Margin</th></tr></thead>
          <tbody>
            <tr><td><strong>Honda Click 125i</strong></td><td class="amt">₱1,243,500</td><td class="amt">₱965,000</td><td class="amt" style="color:var(--green)">₱278,500</td><td class="mono">22.4%</td></tr>
            <tr><td><strong>Yamaha NMAX</strong></td><td class="amt">₱2,126,600</td><td class="amt">₱1,680,000</td><td class="amt" style="color:var(--green)">₱446,600</td><td class="mono">21.0%</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateReportRun(this)">Run Analysis</button>
    <button class="btn btn-sm" onclick="exportCsv('receivables')">Export CSV</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal(`Analysis: ${action}`, html, actions, { width: 'min(860px, calc(100vw - 48px))' });
}

function openReportDemo(action) {
  const branches = typeof branchData !== 'undefined' ? branchData.map(b => `<option value="${esc(b.name)}">${esc(b.name)}</option>`).join('') : '<option>All Branches</option>';
  const html = `
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Date From</label><input type="date" value="2026-05-01">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Date To</label><input type="date" value="2026-05-31">
    </div>
    <div class="orcr-field" style="margin-bottom:12px">
      <label>Branch Filter</label><select><option>All Branches</option>${branches}</select>
    </div>
    <div id="demo-report-results" style="display:none; margin-top:16px;">
      <div class="sp-section-label">Generated Data</div>
      <div class="table-wrap" style="border:1px solid var(--border);">
        <table>
          <thead><tr><th>Reference</th><th>Date</th><th>Amount</th></tr></thead>
          <tbody>
            <tr><td>DEMO-001</td><td>May 10, 2026</td><td class="amt">₱15,000</td></tr>
            <tr><td>DEMO-002</td><td>May 12, 2026</td><td class="amt">₱42,500</td></tr>
            <tr><td>DEMO-003</td><td>May 14, 2026</td><td class="amt">₱8,900</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="simulateReportRun(this)">Run Report</button>
    <button class="btn btn-sm" onclick="exportCsv('sales')">Export CSV</button>
    <button class="btn btn-sm" onclick="window.print()">Print Preview</button>
    <button class="btn btn-sm" onclick="showToast('Demo only: report email prepared successfully.')">Email Report</button>
  `;
  openDemoPanel(`Report: ${action}`, html, actions);
}

function simulateReportRun(btn) {
  flashButton(btn, 'Running...');
  setTimeout(() => {
    const results = document.getElementById('demo-report-results');
    if (results) results.style.display = 'block';
    showToast('Report generated successfully.');
  }, 1000);
}

function openDemoPrintedForm(action) {
  openDemoPanel(`Printed Form: ${action}`, `
    <div class="sp-section-label">Document Preview</div>
    <div class="sp-row"><span class="sp-key">Document Type</span><span class="sp-val">${esc(action)}</span></div>
    <div class="sp-row"><span class="sp-key">Template</span><span class="sp-val">Standard Corporate Layout</span></div>
    <div class="sp-row"><span class="sp-key">Pages</span><span class="sp-val">1</span></div>
  `, `<button class="btn btn-sm btn-primary" onclick="window.print()">Print</button><button class="btn btn-sm" onclick="showToast('Demo only: Form downloaded as PDF.')">Download PDF</button>`);
}

function openDemoProfile(action) {
  if (action === 'Sales Prices') {
    openDemoPanel(`Price List`, `
      <div class="sp-section-label">Current Pricing</div>
      <div class="sp-row"><span class="sp-key">Base Price</span><span class="sp-val">${badge('Active')}</span></div>
      <div class="sp-row"><span class="sp-key">Promo Price</span><span class="sp-val">Scheduled for next month</span></div>
      <div class="sp-row"><span class="sp-key">Last Updated</span><span class="sp-val">${new Date().toLocaleDateString('en-PH')}</span></div>
    `, `<button class="btn btn-sm btn-primary" onclick="showToast('Demo only: Price list updated.')">Update Prices</button><button class="btn btn-sm" onclick="exportCsv('sales')">Export List</button>`);
    return;
  }
  openDemoPanel(`Profile Setup: ${action}`, `
    <div class="sp-section-label">Entity Details</div>
    <div class="sp-row"><span class="sp-key">Total Records</span><span class="sp-val">${Math.floor(Math.random()*200) + 10}</span></div>
    <div class="sp-row"><span class="sp-key">Active</span><span class="sp-val">${badge('Active')}</span></div>
    <div class="sp-row"><span class="sp-key">Last Synced</span><span class="sp-val">${new Date().toLocaleDateString('en-PH')}</span></div>
  `, `<button class="btn btn-sm btn-primary" onclick="showToast('Demo only: New record form opened.')">Add New Record</button><button class="btn btn-sm" onclick="exportCsv('sales')">Export List</button>`);
}

function openDemoPreference(action) {
  openDemoPanel(`Preferences: ${action}`, `
    <div class="sp-section-label">Configuration Settings</div>
    <div class="sp-row"><span class="sp-key">Module</span><span class="sp-val">${esc(action.split(' ')[0])}</span></div>
    <div class="sp-row"><span class="sp-key">Auto-Approval</span><span class="sp-val">Enabled</span></div>
    <div class="sp-row"><span class="sp-key">Default Currency</span><span class="sp-val">PHP</span></div>
    <div class="sp-row"><span class="sp-key">Sync Frequency</span><span class="sp-val">Real-time</span></div>
  `, `<button class="btn btn-sm btn-primary" onclick="showToast('Demo only: Preferences saved.')">Save Settings</button><button class="btn btn-sm" onclick="showToast('Demo only: Settings reset.')">Reset to Default</button>`);
}

function openGenericDemo(action, type) {
  openDemoPanel(
    `${type.charAt(0).toUpperCase() + type.slice(1)}: ${action}`,
    `<div class="sp-section-label">Demo Preview</div>
     <p style="font-size:12.5px; color:var(--text-secondary); line-height:1.5;">This screen is available in the full implementation. Demo preview only for <strong>${esc(action)}</strong>.</p>`,
    `<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`
  );
}

// ===== CLOCK =====
function tick(){
  const now=new Date();
  document.getElementById('tb-clock').textContent=now.toLocaleString('en-PH',{weekday:'short',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit',hour12:true});
}
initTheme();
setInterval(tick,1000);tick();
setInterval(()=>{if(document.getElementById('payroll-live'))renderPayroll()},15000);
initializeDemo();

// ===== LOGIN =====
function doLogin(){
  const u=document.getElementById('l-user').value.trim();
  const p=document.getElementById('l-pass').value.trim();
  if(!u||!p){document.getElementById('l-err').style.display='block';return}
  document.getElementById('l-err').style.display='none';
  const av=u.split(' ').filter(Boolean).map(w=>w[0].toUpperCase()).join('').substring(0,2)||'ST';
  document.getElementById('sb-av').textContent=av;
  document.getElementById('sb-name').textContent=u.charAt(0).toUpperCase()+u.slice(1);
  document.getElementById('login-screen').style.display='none';
  document.getElementById('app').style.display='flex';
  initializeDemo();
  filterInv();filterSO();filterAR();filterCust();filterPO();filterAP();filterGL();filterPayroll();
}
// --- PURCHASES MODULE FULL DEMO ---
function initPurchasesDemoData() {
  let d = JSON.parse(localStorage.getItem('purchasesDemoData'));
  if(!d) {
    d = {
      purchaseRequests: [],
      requisitions: [],
      purchaseOrders: [],
      purchaseReceipts: [],
      landedCosts: [],
      vendors: [
        { id: 'VND-001', name: 'Honda Philippines', contact: 'Juan Dela Cruz', phone: '09171112222', email: 'sales@hondaph.com', address: 'Batangas', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-002', name: 'Yamaha Motor Philippines', contact: 'Maria Santos', phone: '09182223333', email: 'orders@yamaha-motor.com.ph', address: 'Laguna', terms: 'NET15', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-003', name: 'Kawasaki Motors Philippines', contact: 'Pedro Reyes', phone: '09193334444', email: 'info@kawasaki.ph', address: 'Muntinlupa', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-004', name: 'Suzuki Philippines', contact: 'Ana Lim', phone: '09204445555', email: 'sales@suzuki.com.ph', address: 'Laguna', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-005', name: 'TVS Motor Philippines', contact: 'Carlos Chua', phone: '09215556666', email: 'contact@tvs.ph', address: 'Manila', terms: 'CASH', type: 'Distributor', status: 'Active' }
      ],
      vendorPrices: [],
      vendorInventory: [],
      intercompanyPurchaseOrders: [],
      logs: []
    };
    
    const vendorsMap = {'Honda':'Honda Philippines','Yamaha':'Yamaha Motor Philippines','Kawasaki':'Kawasaki Motors Philippines','Suzuki':'Suzuki Philippines','TVS':'TVS Motor Philippines'};
    catalog.forEach(item => {
       const vendor = vendorsMap[item.brand] || 'Honda Philippines';
       d.vendorPrices.push({ vendor, model: item.model, standardCost: item.price * 0.75, discount: '5%', effective: '2026-01-01', status: 'Active' });
       d.vendorInventory.push({ vendor, model: item.model, available: Math.floor(Math.random()*100)+10, leadTime: '7 Days', lastPurchase: '2026-04-15', status: 'Active' });
    });

    localStorage.setItem('purchasesDemoData', JSON.stringify(d));
  }
  return d;
}

function updateLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    }
  }
}

function openPurchasesDemo(action, type) {
  const data = initPurchasesDemoData();
  
  if(type === 'po-shortcut') {
    if(action === 'New Purchase Order') openPoShortcutModal(data);
    else if(action === 'New Purchase Receipt') openReceiptShortcutModal(data);
    else if(action === 'New Purchase Request') openRequestShortcutModal(data);
    else if(action === 'New Vendor') openVendorShortcutModal(data);
  }
  else if(type === 'po-transaction') {
    if(action === 'Requests') openPoRequestsModal(data);
    else if(action === 'Requisitions') openPoRequisitionsModal(data);
    else if(action === 'Purchase Orders') openPoOrdersModal(data);
    else if(action === 'Purchase Receipts') openPoReceiptsModal(data);
    else if(action === 'Landed Costs') openPoLandedCostsModal(data);
  }
  else if(type === 'po-profile') {
    if(action === 'Vendors') openPoVendorsModal(data);
    else if(action === 'Vendor Prices') openPoVendorPricesModal(data);
    else if(action === 'Vendor Inventory') openPoVendorInventoryModal(data);
  }
  else if(type === 'po-process') {
    if(action === 'Create Purchase Orders') openProcessCreatePO(data);
    else if(action === 'Print/Email Purchase Orders') openProcessEmailPO(data);
    else if(action === 'Generate Intercompany Purchase Orders') openProcessIntercompanyPO(data);
  }
  else if(type === 'po-printed-form') {
    if(action === 'Item Request') openFormItemRequest(data);
    else if(action === 'Purchase Order') openFormPO(data);
    else if(action === 'Purchase Receipt') openFormReceipt(data);
  }
  else if(type === 'po-report') {
    openPoReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openPoShortcutModal(data) {
  const vOpts = data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  const iOpts = catalog.map(c=>`<option value="${c.model}">${c.brand} ${c.model}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="po-vnd">${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch / Destination</label><select id="po-branch">${bOpts}</select></div>
      <div class="demo-form-row"><label>Order Date</label><input type="date" id="po-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Expected Delivery Date</label><input type="date" id="po-del" value="2026-05-22"></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="po-item">${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="po-qty" value="10"></div>
      <div class="demo-form-row"><label>Unit Cost</label><input type="number" id="po-cost" value="65000"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="po-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="po-rem" placeholder="Optional notes"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitNewPO(this)">Submit Purchase Order</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>`;
  openCenterModal('New Purchase Order', html, actions);
}

function submitNewPO(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const poNo = 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0');
    const qty = parseFloat(document.getElementById('po-qty').value)||0;
    const cost = parseFloat(document.getElementById('po-cost').value)||0;
    data.purchaseOrders.unshift({
      no: poNo,
      date: document.getElementById('po-date').value,
      vendor: document.getElementById('po-vnd').value,
      branch: document.getElementById('po-branch').value,
      item: document.getElementById('po-item').value,
      qty, cost, total: qty * cost,
      delivery: document.getElementById('po-del').value,
      status: 'Open'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase order submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openReceiptShortcutModal(data) {
  const vOpts = data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const poOpts = data.purchaseOrders.map(p=>`<option value="${p.no}">${p.no} - ${p.item}</option>`).join('') || '<option value="">No Open POs</option>';
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Purchase Order No.</label><select id="pr-po">${poOpts}</select></div>
      <div class="demo-form-row"><label>Vendor</label><select id="pr-vnd">${vOpts}</select></div>
      <div class="demo-form-row"><label>Receipt Date</label><input type="date" id="pr-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Received By</label><input type="text" id="pr-by" value="Warehouse Staff"></div>
      <div class="demo-form-row"><label>Quantity Received</label><input type="number" id="pr-qty" value="10"></div>
      <div class="demo-form-row"><label>Warehouse / Branch</label><select><option>Main Warehouse</option></select></div>
      <div class="demo-form-row"><label>Condition</label><select id="pr-cond"><option>Good</option><option>Damaged</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="pr-rem"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitNewReceipt(this)">Save Receipt</button><button class="btn btn-sm" onclick="showToast('Receipt printed');">Print Receipt</button>`;
  openCenterModal('New Purchase Receipt', html, actions);
}

function submitNewReceipt(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const recNo = 'PR-' + String(data.purchaseReceipts.length + 5001).padStart(4, '0');
    const poNo = document.getElementById('pr-po').value;
    data.purchaseReceipts.unshift({
      no: recNo, poNo,
      vendor: document.getElementById('pr-vnd').value,
      date: document.getElementById('pr-date').value,
      item: 'Unit Items', // simplified
      qty: parseFloat(document.getElementById('pr-qty').value)||0,
      condition: document.getElementById('pr-cond').value,
      status: 'Received'
    });
    const po = data.purchaseOrders.find(p=>p.no===poNo);
    if(po) po.status = 'Received';
    
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase receipt saved successfully.');
    closeCenterModal();
  }, 600);
}

function openRequestShortcutModal(data) {
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  const iOpts = catalog.map(c=>`<option value="${c.model}">${c.brand} ${c.model}</option>`).join('');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Requesting Branch</label><select id="req-br">${bOpts}</select></div>
      <div class="demo-form-row"><label>Requested By</label><input type="text" id="req-by" value="Branch Manager"></div>
      <div class="demo-form-row"><label>Request Date</label><input type="date" id="req-date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="req-item">${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity Requested</label><input type="number" id="req-qty" value="5"></div>
      <div class="demo-form-row"><label>Priority</label><select id="req-pri"><option>Normal</option><option>High</option><option>Urgent</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Reason / Notes</label><input type="text" id="req-note" placeholder="E.g., Stock depletion"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitNewRequest(this)">Submit for Approval</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Request</button>`;
  openCenterModal('New Purchase Request', html, actions);
}

function submitNewRequest(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const no = 'REQ-' + String(data.purchaseRequests.length + 101).padStart(4, '0');
    data.purchaseRequests.unshift({
      no,
      date: document.getElementById('req-date').value,
      branch: document.getElementById('req-br').value,
      by: document.getElementById('req-by').value,
      item: document.getElementById('req-item').value,
      qty: document.getElementById('req-qty').value,
      priority: document.getElementById('req-pri').value,
      status: 'Pending Approval'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo purchase request submitted for approval.');
    closeCenterModal();
  }, 600);
}

function openVendorShortcutModal(data) {
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="vnd-name"></div>
      <div class="demo-form-row"><label>Contact Person</label><input type="text" id="vnd-contact"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="vnd-phone"></div>
      <div class="demo-form-row"><label>Email</label><input type="email" id="vnd-email"></div>
      <div class="demo-form-row"><label>Address</label><input type="text" id="vnd-addr"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="vnd-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row"><label>Vendor Type</label><select id="vnd-type"><option>Manufacturer</option><option>Distributor</option><option>Service Provider</option></select></div>
      <div class="demo-form-row"><label>Status</label><select id="vnd-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitNewVendor(this)">Save Vendor</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>`;
  openCenterModal('New Vendor', html, actions);
}

function submitNewVendor(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const id = 'VND-' + String(data.vendors.length + 1).padStart(3, '0');
    data.vendors.push({
      id,
      name: document.getElementById('vnd-name').value || 'Unknown Vendor',
      contact: document.getElementById('vnd-contact').value,
      phone: document.getElementById('vnd-phone').value,
      email: document.getElementById('vnd-email').value,
      address: document.getElementById('vnd-addr').value,
      terms: document.getElementById('vnd-terms').value,
      type: document.getElementById('vnd-type').value,
      status: document.getElementById('vnd-status').value
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Demo vendor saved successfully.');
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function exportPurchasesCsv(filename, arrayData) {
  if(!arrayData || arrayData.length===0) { showToast('No data to export'); return; }
  exportRowsToCsv(filename, arrayData);
}

function openPoRequestsModal(data) {
  const rows = data.purchaseRequests.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.branch}</td><td>${r.by}</td>
    <td>${r.item}</td><td class="mono" style="text-align:center">${r.qty}</td><td>${badge(r.priority==='Urgent'?'Late':(r.priority==='High'?'Partial':'Normal'))}</td>
    <td>${badge(r.status==='Approved'?'Approved':(r.status==='Rejected'?'Cancelled':'Pending'))}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Request')">View</button>
      ${r.status.includes('Pending') ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateReqStatus('${r.no}','Approved', this)">Approve</button> <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="updateReqStatus('${r.no}','Rejected', this)">Reject</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="9" style="text-align:center;color:var(--text-tertiary)">No requests</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Request No.</th><th>Date</th><th>Branch</th><th>Requested By</th><th>Item</th><th style="text-align:center">Qty</th><th>Priority</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('requests.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseRequests)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Purchase Requests', html, actions, {width: '1000px'});
}

function updateReqStatus(no, stat, btn) {
  updateLocalData('purchaseRequests', 'no', no, {status: stat});
  showToast('Request updated successfully.');
  openPoRequestsModal(JSON.parse(localStorage.getItem('purchasesDemoData')));
}

function openPoRequisitionsModal(data) {
  const rows = data.purchaseRequests.filter(r=>r.status==='Approved').map(r=>`<tr>
    <td class="mono">RQZ-${r.no.split('-')[1]}</td><td class="mono dim">${r.no}</td><td>${r.branch}</td>
    <td>${r.item}</td><td class="mono" style="text-align:center">${r.qty}</td><td>System Admin</td><td>${badge('Approved')}</td>
    <td style="text-align:center">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="reqConvertToPo('${r.no}', this)">Convert to PO</button>
    </td>
  </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No approved requisitions</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Requisition No.</th><th>Request No.</th><th>Branch</th><th>Item</th><th style="text-align:center">Qty</th><th>Approved By</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('requisitions.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseRequests.filter(r=>r.status==='Approved'))">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Requisitions', html, actions, {width: '950px'});
}

function reqConvertToPo(no, btn) {
  setButtonLoading(btn, 'Converting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const req = data.purchaseRequests.find(r=>r.no===no);
    if(req) req.status = 'Converted';
    
    data.purchaseOrders.unshift({
      no: 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0'),
      date: new Date().toISOString().split('T')[0],
      vendor: 'Honda Philippines',
      branch: req.branch,
      item: req.item,
      qty: parseInt(req.qty)||1,
      cost: 65000, total: 65000*(parseInt(req.qty)||1),
      delivery: '2026-06-01',
      status: 'Open'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    showToast('Requisition converted to Purchase Order.');
    openPoRequisitionsModal(data);
  }, 600);
}

function openPoOrdersModal(data) {
  const rows = data.purchaseOrders.map(p=>`<tr>
    <td class="mono">${p.no}</td><td class="dim">${p.date}</td><td><strong>${p.vendor}</strong></td><td>${p.branch}</td>
    <td>${p.item}</td><td class="mono" style="text-align:center">${p.qty}</td><td class="amt">&#8369;${fmt(p.cost)}</td><td class="amt">&#8369;${fmt(p.total)}</td>
    <td class="dim">${p.delivery}</td><td>${badge(p.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing PO')">View PO</button>
      ${p.status==='Open' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('${p.no}','Approved', this)">Approve</button>` : ''}
      ${p.status==='Approved' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('${p.no}','Ordered', this)">Mark Ordered</button>` : ''}
      ${p.status==='Ordered' ? `<button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="updatePoStatus('${p.no}','Received', this)">Mark Received</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="11" style="text-align:center;color:var(--text-tertiary)">No purchase orders</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>PO No.</th><th>Date</th><th>Vendor</th><th>Branch</th><th>Item</th><th style="text-align:center">Qty</th><th>Unit Cost</th><th>Total Amount</th><th>Expected Delivery</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('purchase-orders.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseOrders)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Purchase Orders', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

function updatePoStatus(no, stat, btn) {
  updateLocalData('purchaseOrders', 'no', no, {status: stat});
  showToast('PO status updated.');
  openPoOrdersModal(JSON.parse(localStorage.getItem('purchasesDemoData')));
}

function openPoReceiptsModal(data) {
  const rows = data.purchaseReceipts.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="mono dim">${r.poNo}</td><td><strong>${r.vendor}</strong></td><td class="dim">${r.date}</td>
    <td>${r.item}</td><td class="mono" style="text-align:center">${r.qty}</td><td>Warehouse</td><td>${r.condition}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Receipt')">View Receipt</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Receipt printed')">Print</button>
    </td>
  </tr>`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No purchase receipts</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Receipt No.</th><th>PO No.</th><th>Vendor</th><th>Receipt Date</th><th>Item</th><th style="text-align:center">Qty Recv</th><th>Branch</th><th>Condition</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('purchase-receipts.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).purchaseReceipts)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Purchase Receipts', html, actions, {width: '1050px'});
}

function openPoLandedCostsModal(data) {
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Landed Cost No.</th><th>PO No.</th><th>Vendor</th><th>Freight</th><th>Insurance</th><th>Duties</th><th>Other</th><th>Total</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody><tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No landed cost records yet. Add one via Data View.</td></tr></tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="showToast('Adding landed cost...')">Add Landed Cost</button><button class="btn btn-sm" onclick="showToast('Allocating costs...')">Allocate Cost</button><button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Landed Costs', html, actions, {width: '950px'});
}

// ======================= PROFILES =======================
function openPoVendorsModal(data) {
  const rows = data.vendors.map(v=>`<tr>
    <td class="mono">${v.id}</td><td><strong>${v.name}</strong></td><td>${v.contact}</td><td class="mono dim">${v.phone}</td>
    <td class="dim">${v.email}</td><td class="mono">${v.terms}</td><td>${badge(v.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" onclick="openVendorProfileModal('${v.id}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
  </tr>`).join('');
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Vendor ID</th><th>Vendor Name</th><th>Contact Person</th><th>Contact Number</th><th>Email</th><th>Terms</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('vendors.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Vendors', html, actions, {width: '1000px'});
}

function openVendorProfileModal(id) {
  const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
  const v = data.vendors.find(x=>x.id===id);
  if(!v) return;
  
  const pos = data.purchaseOrders.filter(p=>p.vendor===v.name);
  const receipts = data.purchaseReceipts.filter(r=>r.vendor===v.name);
  const total = pos.reduce((sum, p) => sum + (p.total || 0), 0);
  
  const poHtml = pos.length ? pos.map(p=>`<tr><td class="mono">${p.no}</td><td class="dim">${p.date}</td><td>${p.item}</td><td class="mono">${p.qty}</td><td class="amt">&#8369;${fmt(p.total)}</td><td>${badge(p.status)}</td></tr>`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No orders</td></tr>';
  const recHtml = receipts.length ? receipts.map(r=>`<tr><td class="mono">${r.no}</td><td class="mono">${r.poNo}</td><td class="dim">${r.date}</td><td>${r.item}</td><td class="mono">${r.qty}</td><td>${badge(r.status)}</td></tr>`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No receipts</td></tr>';

  const html = `
    <div class="sp-section" id="vnd-view-mode">
      <div class="sp-section-label">Vendor Information</div>
      <div class="sp-row"><span class="sp-key">Vendor ID</span><span class="sp-val mono">${v.id}</span></div>
      <div class="sp-row"><span class="sp-key">Vendor Name</span><span class="sp-val"><strong>${v.name}</strong></span></div>
      <div class="sp-row"><span class="sp-key">Contact Person</span><span class="sp-val">${v.contact}</span></div>
      <div class="sp-row"><span class="sp-key">Contact Number</span><span class="sp-val mono">${v.phone}</span></div>
      <div class="sp-row"><span class="sp-key">Email</span><span class="sp-val">${v.email}</span></div>
      <div class="sp-row"><span class="sp-key">Payment Terms</span><span class="sp-val mono">${v.terms}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(v.status)}</span></div>
      <div class="sp-row"><span class="sp-key">Total Purchase Amount</span><span class="sp-val amt" style="color:var(--green)">&#8369;${fmt(total)}</span></div>
    </div>
    <div class="sp-section" id="vnd-edit-mode" style="display:none">
      <div class="demo-form-grid">
        <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="ev-name" value="${v.name}"></div>
        <div class="demo-form-row"><label>Contact Person</label><input type="text" id="ev-contact" value="${v.contact}"></div>
        <div class="demo-form-row"><label>Contact Number</label><input type="text" id="ev-phone" value="${v.phone}"></div>
        <div class="demo-form-row"><label>Email</label><input type="email" id="ev-email" value="${v.email}"></div>
        <div class="demo-form-row"><label>Payment Terms</label><input type="text" id="ev-terms" value="${v.terms}"></div>
        <div class="demo-form-row"><label>Status</label><select id="ev-status"><option ${v.status==='Active'?'selected':''}>Active</option><option ${v.status==='Inactive'?'selected':''}>Inactive</option></select></div>
        <div class="demo-form-row" style="grid-column:1/-1;display:flex;gap:10px">
          <button class="btn btn-sm btn-primary" onclick="saveEditVendor('${v.id}')">Save Changes</button>
          <button class="btn btn-sm" onclick="document.getElementById('vnd-edit-mode').style.display='none';document.getElementById('vnd-view-mode').style.display='block';">Cancel</button>
        </div>
      </div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Purchase Order History</div>
      <div class="table-wrap"><table><thead><tr><th>PO No.</th><th>Date</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead><tbody>${poHtml}</tbody></table></div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Receipt History</div>
      <div class="table-wrap"><table><thead><tr><th>Receipt No.</th><th>PO No.</th><th>Date</th><th>Item</th><th>Qty</th><th>Status</th></tr></thead><tbody>${recHtml}</tbody></table></div>
    </div>
    <div class="sp-section">
      <div class="sp-section-label">Activity Log</div>
      <div class="sp-row"><span class="dim" style="font-size:12px">Last activity: Demo data generation.</span></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm" onclick="document.getElementById('vnd-view-mode').style.display='none';document.getElementById('vnd-edit-mode').style.display='block';">Edit Demo Vendor</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Vendor...');">Export Vendor CSV</button>
    <button class="btn btn-sm" onclick="showToast('Profile printed');setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm btn-primary" onclick="openPoVendorsModal(JSON.parse(localStorage.getItem('purchasesDemoData')))">Close</button>
  `;
  openCenterModal('Vendor Profile: ' + v.name, html, actions, {width: '900px'});
}

function saveEditVendor(id) {
  updateLocalData('vendors', 'id', id, {
    name: document.getElementById('ev-name').value,
    contact: document.getElementById('ev-contact').value,
    phone: document.getElementById('ev-phone').value,
    email: document.getElementById('ev-email').value,
    terms: document.getElementById('ev-terms').value,
    status: document.getElementById('ev-status').value
  });
  showToast('Vendor profile updated successfully.');
  openVendorProfileModal(id);
}

function openPoVendorPricesModal(data) {
  const rows = data.vendorPrices.map(p=>`<tr>
    <td><strong>${p.vendor}</strong></td><td>${p.model}</td><td class="amt">&#8369;${fmt(p.standardCost)}</td>
    <td class="mono">${p.discount}</td><td class="mono dim">${p.effective}</td><td>${badge(p.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Editing price')">Edit Price</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>Vendor</th><th>Unit Model</th><th>Standard Cost</th><th>Discount</th><th>Effective Date</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('prices.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendorPrices)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Vendor Prices', html, actions, {width: '850px'});
}

function openPoVendorInventoryModal(data) {
  const rows = data.vendorInventory.map(p=>`<tr>
    <td><strong>${p.vendor}</strong></td><td>${p.model}</td><td class="mono" style="text-align:center">${p.available}</td>
    <td class="dim">${p.leadTime}</td><td class="mono dim">${p.lastPurchase}</td><td>${badge(p.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Stock requested')">Request Stock</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>Vendor</th><th>Unit Model</th><th style="text-align:center">Available Qty</th><th>Lead Time</th><th>Last Purchase</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportPurchasesCsv('inventory.csv', JSON.parse(localStorage.getItem('purchasesDemoData')).vendorInventory)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Vendor Inventory', html, actions, {width: '900px'});
}

// ======================= PROCESSES =======================
function openProcessCreatePO(data) {
  const vOpts = data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  const reqs = data.purchaseRequests.filter(r=>r.status==='Approved');
  const rows = reqs.map(r=>`<tr>
    <td class="mono">${r.no}</td><td>${r.branch}</td><td>${r.item}</td><td class="mono">${r.qty}</td>
    <td><select class="req-ven-sel" style="width:120px;padding:2px;font-size:11px">${vOpts}</select></td>
    <td>${badge(r.status)}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No approved requests to convert</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Target Branch</label><select><option>All</option>${bOpts}</select></div>
      <div class="demo-form-row"><label>Target Vendor</label><select><option>Select per item</option>${vOpts}</select></div>
      <div class="demo-form-row"><label>Request Status</label><select><option>Approved</option></select></div>
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Request No.</th><th>Branch</th><th>Item</th><th>Qty</th><th>Vendor</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runGeneratePOProcess(this)" ${reqs.length===0?'disabled':''}>Generate Purchase Orders</button>
    <button class="btn btn-sm" onclick="showToast('Printing Process Log...')">Print Process Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Create Purchase Orders', html, actions, {width: '850px'});
}

function runGeneratePOProcess(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const reqs = data.purchaseRequests.filter(r=>r.status==='Approved');
    let generatedCount = 0;
    reqs.forEach(r => {
      r.status = 'Converted';
      const cost = Math.floor(Math.random()*20000)+60000;
      data.purchaseOrders.unshift({
        no: 'PO-' + String(data.purchaseOrders.length + 1001).padStart(4, '0'),
        date: new Date().toISOString().split('T')[0],
        vendor: 'Honda Philippines', // simulated selection
        branch: r.branch,
        item: r.item,
        qty: parseInt(r.qty)||1,
        cost, total: cost*(parseInt(r.qty)||1),
        delivery: '2026-05-30',
        status: 'Open'
      });
      generatedCount++;
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Process Complete';
    btn.disabled = true;
    showToast(`Demo purchase orders generated successfully.`);
  }, 1200);
}

function openProcessEmailPO(data) {
  const pos = data.purchaseOrders.filter(p=>p.status==='Open');
  const rows = pos.map(p=>`<tr>
    <td class="mono"><input type="checkbox" checked> ${p.no}</td><td><strong>${p.vendor}</strong></td>
    <td class="dim">Purchase Order</td><td class="dim">orders@${p.vendor.replace(/\s/g,'').toLowerCase()}.com</td>
    <td>Email</td><td>${badge('Pending')}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No open POs to send</td></tr>';

  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Purchase Order</label><select><option>All Open</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Recipient Email</label><input type="email" placeholder="Optional override"></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end"><label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="checkbox" checked> Include Attachments</label></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Message</label><textarea rows="2" style="width:100%;border:1px solid var(--border);border-radius:4px;padding:8px">Please find attached the latest Purchase Orders.</textarea></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>PO No.</th><th>Vendor</th><th>Document Type</th><th>Recipient Email</th><th>Method</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runEmailPOProcess(this)" ${pos.length===0?'disabled':''}>Send Demo Email</button>
    <button class="btn btn-sm" onclick="showToast('Email Prepared.')">Prepare Email</button>
    <button class="btn btn-sm" onclick="showToast('Print preview mode')">Print Preview</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Print/Email Purchase Orders', html, actions, {width: '900px'});
}

function runEmailPOProcess(btn) {
  setButtonLoading(btn, 'Sending Emails...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Emails Sent';
    btn.disabled = true;
    showToast('Demo purchase order emails processed successfully.');
  }, 1000);
}

function openProcessIntercompanyPO(data) {
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  const iOpts = catalog.map(c=>`<option value="${c.model}">${c.brand} ${c.model}</option>`).join('');
  
  const rows = data.intercompanyPurchaseOrders.map(p=>`<tr>
    <td class="mono">${p.no}</td><td>${p.source}</td><td>${p.dest}</td><td>${p.item}</td>
    <td class="mono">${p.qty}</td><td>${badge(p.status)}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No recent intercompany orders</td></tr>';

  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Source Branch</label><select id="ip-src">${bOpts}</select></div>
      <div class="demo-form-row"><label>Destination Branch</label><select id="ip-dest">${bOpts}</select></div>
      <div class="demo-form-row"><label>Vendor / Internal Supplier</label><select><option>NEXII Internal Logistics</option></select></div>
      <div class="demo-form-row"><label>Transaction Date</label><input type="date" value="2026-05-15"></div>
      <div class="demo-form-row"><label>Item / Model</label><select id="ip-item">${iOpts}</select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="ip-qty" value="5"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Reason</label><input type="text" placeholder="Stock balancing"></div>
    </div>
    <div class="table-wrap">
      <table><thead><tr><th>Intercompany PO</th><th>Source Branch</th><th>Destination Branch</th><th>Item</th><th>Qty</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runIntercompanyPOProcess(this)">Generate Intercompany PO</button>
    <button class="btn btn-sm" onclick="showToast('Exporting log...')">Export Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Generate Intercompany Purchase Orders', html, actions, {width: '850px'});
}

function runIntercompanyPOProcess(btn) {
  setButtonLoading(btn, 'Processing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    const src = document.getElementById('ip-src').value;
    const dest = document.getElementById('ip-dest').value;
    const item = document.getElementById('ip-item').value;
    const qty = document.getElementById('ip-qty').value;
    data.intercompanyPurchaseOrders.unshift({
      no: 'IPO-2026-' + String(data.intercompanyPurchaseOrders.length + 100).padStart(3, '0'),
      source: src, dest, item, qty, status: 'Generated'
    });
    localStorage.setItem('purchasesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    showToast('Demo intercompany purchase order generated successfully.');
    openProcessIntercompanyPO(data); // refresh
  }, 800);
}

// ======================= PRINTED FORMS =======================
function openFormItemRequest(data) {
  const req = data.purchaseRequests[0] || {no: 'REQ-DEMO', branch: 'Main', by: 'Admin', date: '2026-05-15', item: 'Sample Item', qty: 10, reason: 'Demo', status: 'Pending'};
  const html = `
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">ITEM REQUEST FORM</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">Request No:</td><td style="padding:4px">${req.no}</td><td style="padding:4px;width:120px;font-weight:bold">Date:</td><td style="padding:4px">${req.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Branch:</td><td style="padding:4px">${req.branch}</td><td style="padding:4px;font-weight:bold">Requested By:</td><td style="padding:4px">${req.by}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Status:</td><td style="padding:4px">${req.status}</td><td style="padding:4px;font-weight:bold"></td><td style="padding:4px"></td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty</th><th style="border:1px solid #000;padding:8px">Reason</th></tr></thead>
        <tbody><tr><td style="border:1px solid #000;padding:8px">${req.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">${req.qty}</td><td style="border:1px solid #000;padding:8px">${req.reason||'Stock Replenishment'}</td></tr></tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Print Preview: Item Request', html, actions, {width:'800px'});
}

function openFormPO(data) {
  const po = data.purchaseOrders[0] || {no: 'PO-DEMO', date: '2026-05-15', vendor: 'Sample Vendor', branch: 'Main', item: 'Sample Item', qty: 10, cost: 50000, total: 500000, delivery: '2026-05-30'};
  const html = `
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">PURCHASE ORDER</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">PO No:</td><td style="padding:4px">${po.no}</td><td style="padding:4px;width:120px;font-weight:bold">Order Date:</td><td style="padding:4px">${po.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Vendor:</td><td style="padding:4px"><strong>${po.vendor}</strong></td><td style="padding:4px;font-weight:bold">Delivery Date:</td><td style="padding:4px">${po.delivery}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Ship To:</td><td style="padding:4px">${po.branch}</td><td style="padding:4px;font-weight:bold">Terms:</td><td style="padding:4px">NET30</td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty</th><th style="border:1px solid #000;padding:8px">Unit Cost</th><th style="border:1px solid #000;padding:8px">Total Amount</th></tr></thead>
        <tbody>
          <tr><td style="border:1px solid #000;padding:8px">${po.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">${po.qty}</td><td style="border:1px solid #000;padding:8px;text-align:right">&#8369;${fmt(po.cost)}</td><td style="border:1px solid #000;padding:8px;text-align:right">&#8369;${fmt(po.total)}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print Purchase Order</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Print Preview: Purchase Order', html, actions, {width:'800px'});
}

function openFormReceipt(data) {
  const rec = data.purchaseReceipts[0] || {no: 'PR-DEMO', poNo: 'PO-DEMO', date: '2026-05-15', vendor: 'Sample Vendor', item: 'Sample Item', qty: 10, condition: 'Good'};
  const html = `
    <div style="padding:20px;background:#fff;border:1px solid #ddd;color:#000">
      <h2 style="text-align:center;margin:0 0 10px 0">NEXII MOTORCYCLE DEALERSHIP</h2>
      <h3 style="text-align:center;margin:0 0 20px 0;color:#555">RECEIVING REPORT</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
        <tr><td style="padding:4px;width:120px;font-weight:bold">Receipt No:</td><td style="padding:4px">${rec.no}</td><td style="padding:4px;width:120px;font-weight:bold">Receipt Date:</td><td style="padding:4px">${rec.date}</td></tr>
        <tr><td style="padding:4px;font-weight:bold">Vendor:</td><td style="padding:4px">${rec.vendor}</td><td style="padding:4px;font-weight:bold">PO Reference:</td><td style="padding:4px">${rec.poNo}</td></tr>
      </table>
      <table style="width:100%;border-collapse:collapse;border:1px solid #000">
        <thead><tr style="background:#f0f0f0"><th style="border:1px solid #000;padding:8px">Item Description</th><th style="border:1px solid #000;padding:8px">Qty Received</th><th style="border:1px solid #000;padding:8px">Condition</th></tr></thead>
        <tbody><tr><td style="border:1px solid #000;padding:8px">${rec.item}</td><td style="border:1px solid #000;padding:8px;text-align:center">${rec.qty}</td><td style="border:1px solid #000;padding:8px;text-align:center">${rec.condition}</td></tr></tbody>
      </table>
      <div style="margin-top:40px;display:flex;justify-content:space-between">
        <div style="width:45%;border-top:1px solid #000;text-align:center;padding-top:8px">Received By / Signature</div>
        <div style="width:45%;border-top:1px solid #000;text-align:center;padding-top:8px">Inspected By / Signature</div>
      </div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="showToast('Form sent to printer.');setTimeout(()=>window.print(),500)">Print Receipt</button><button class="btn btn-sm" onclick="showToast('PDF Exported')">Export PDF Demo</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Print Preview: Purchase Receipt', html, actions, {width:'800px'});
}

// ======================= REPORTS =======================
function openPoReportModal(reportName, data) {
  const vOpts = `<option value="">All Vendors</option>` + data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const bOpts = `<option value="">All Branches</option>` + branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Vendor</label><select>${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select>${bOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option><option>Open</option><option>Received</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runDemoPoReport(this, '${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="po-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1000px, calc(100vw - 48px))'});
}

function runDemoPoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('purchasesDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'Purchase Order Summary') {
      thead = `<tr><th>Vendor</th><th style="text-align:center">Total POs</th><th style="text-align:center">Total Quantity</th><th>Total Amount</th><th>Open Amount</th><th>Status</th></tr>`;
      rows = `<tr><td><strong>Honda Philippines</strong></td><td class="mono" style="text-align:center">12</td><td class="mono" style="text-align:center">120</td><td class="amt">&#8369;8,500,000</td><td class="amt">&#8369;1,200,000</td><td>${badge('Active')}</td></tr>`;
    } else if (reportName === 'Purchase Order Details by Vendor' || reportName.includes('Vendor')) {
      thead = `<tr><th>PO No.</th><th>Vendor</th><th>Item</th><th style="text-align:center">Quantity</th><th>Total Amount</th><th>Status</th></tr>`;
      rows = data.purchaseOrders.map(p=>`<tr><td class="mono">${p.no}</td><td><strong>${p.vendor}</strong></td><td>${p.item}</td><td class="mono" style="text-align:center">${p.qty}</td><td class="amt">&#8369;${fmt(p.total)}</td><td>${badge(p.status)}</td></tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No data available</td></tr>';
    } else if (reportName.includes('Inventory')) {
      thead = `<tr><th>Item</th><th>Vendor</th><th style="text-align:center">Qty Ordered</th><th style="text-align:center">Qty Received</th><th style="text-align:center">Remaining</th><th>Total Cost</th></tr>`;
      rows = data.purchaseOrders.map(p=>`<tr><td>${p.item}</td><td><strong>${p.vendor}</strong></td><td class="mono" style="text-align:center">${p.qty}</td><td class="mono" style="text-align:center">${p.status==='Received'?p.qty:0}</td><td class="mono" style="text-align:center">${p.status==='Received'?0:p.qty}</td><td class="amt">&#8369;${fmt(p.total)}</td></tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No data available</td></tr>';
    } else if (reportName === 'Purchase Receipt Details by Vendor') {
      thead = `<tr><th>Receipt No.</th><th>Vendor</th><th>PO No.</th><th>Item</th><th style="text-align:center">Qty Received</th><th>Receipt Date</th><th>Status</th></tr>`;
      rows = data.purchaseReceipts.map(r=>`<tr><td class="mono">${r.no}</td><td><strong>${r.vendor}</strong></td><td class="mono">${r.poNo}</td><td>${r.item}</td><td class="mono" style="text-align:center">${r.qty}</td><td>${r.date}</td><td>${badge(r.status)}</td></tr>`).join('');
    } else {
      thead = `<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>`;
      rows = `<tr><td class="mono">REC-001</td><td class="dim">2026-05-15</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>${badge('Active')}</td></tr>`;
    }
    
    document.getElementById('po-report-body').innerHTML = `<table><thead>${thead}</thead><tbody>${rows}</tbody></table>`;
    resetButtonLoading(btn);
    showToast('Demo report generated successfully.');
  }, 800);
}
// --- END PURCHASES MODULE DEMO ---
// --- PAYABLES MODULE FULL DEMO ---
function initPayablesDemoData() {
  let d = JSON.parse(localStorage.getItem('payablesDemoData'));
  if(!d) {
    d = {
      bills: [],
      adjustments: [],
      checksAndPayments: [],
      vendors: [
        { id: 'VND-001', name: 'Honda Philippines', contact: 'Juan Dela Cruz', phone: '09171112222', email: 'sales@hondaph.com', address: 'Batangas', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-002', name: 'Yamaha Motor Philippines', contact: 'Maria Santos', phone: '09182223333', email: 'orders@yamaha-motor.com.ph', address: 'Laguna', terms: 'NET15', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-003', name: 'Kawasaki Motors Philippines', contact: 'Pedro Reyes', phone: '09193334444', email: 'info@kawasaki.ph', address: 'Muntinlupa', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-004', name: 'Suzuki Philippines', contact: 'Ana Lim', phone: '09204445555', email: 'sales@suzuki.com.ph', address: 'Laguna', terms: 'NET30', type: 'Manufacturer', status: 'Active' },
        { id: 'VND-005', name: 'TVS Motor Philippines', contact: 'Carlos Chua', phone: '09215556666', email: 'contact@tvs.ph', address: 'Manila', terms: 'CASH', type: 'Distributor', status: 'Active' },
        { id: 'VND-006', name: 'Office Supplies PH', contact: 'John Doe', phone: '09223334444', email: 'sales@officesupplies.ph', address: 'Makati', terms: 'NET15', type: 'Supplier', status: 'Active' },
        { id: 'VND-007', name: 'Logistics Partner Inc.', contact: 'Jane Smith', phone: '09334445555', email: 'ops@logistics.ph', address: 'Pasay', terms: 'NET30', type: 'Service', status: 'Active' },
        { id: 'VND-008', name: 'Utility Provider', contact: 'Customer Service', phone: '1622-0000', email: 'billing@utility.ph', address: 'Quezon City', terms: 'Due on Receipt', type: 'Service', status: 'Active' }
      ],
      creditTerms: [
        { code: 'NET15', desc: 'Net 15 Days', dueDays: 15, discount: '0%', status: 'Active' },
        { code: 'NET30', desc: 'Net 30 Days', dueDays: 30, discount: '0%', status: 'Active' },
        { code: 'CASH', desc: 'Cash on Delivery', dueDays: 0, discount: '0%', status: 'Active' },
        { code: 'DUE-REC', desc: 'Due on Receipt', dueDays: 0, discount: '0%', status: 'Active' }
      ],
      paymentBatches: [],
      intercompanyDocuments: [],
      logs: []
    };
    
    // Generate some demo bills
    d.bills.push({ no: 'BILL-1001', date: '2026-05-10', due: '2026-06-09', vendor: 'Honda Philippines', branch: 'Manila Main', desc: 'Motorcycle unit purchase', amount: 850000, paid: 0, balance: 850000, status: 'Open' });
    d.bills.push({ no: 'BILL-1002', date: '2026-05-12', due: '2026-05-27', vendor: 'Yamaha Motor Philippines', branch: 'Quezon City North', desc: 'Spare parts purchase', amount: 150000, paid: 0, balance: 150000, status: 'Open' });
    d.bills.push({ no: 'BILL-1003', date: '2026-05-14', due: '2026-05-29', vendor: 'Office Supplies PH', branch: 'Makati Ayala', desc: 'Office supplies', amount: 12500, paid: 12500, balance: 0, status: 'Closed' });
    d.bills.push({ no: 'BILL-1004', date: '2026-05-01', due: '2026-05-15', vendor: 'Utility Provider', branch: 'Manila Main', desc: 'Utilities', amount: 45000, paid: 0, balance: 45000, status: 'Open' });
    
    // Generate demo payments
    d.checksAndPayments.push({ no: 'PAY-5001', date: '2026-05-15', vendor: 'Office Supplies PH', billNo: 'BILL-1003', method: 'Check', bank: 'BDO-Main', ref: 'CHK-00123', amount: 12500, status: 'Released' });

    localStorage.setItem('payablesDemoData', JSON.stringify(d));
  }
  return d;
}

function updatePayablesLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('payablesDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
    }
  }
}

function openPayablesDemo(action, type) {
  const data = initPayablesDemoData();
  
  if(type === 'ap-shortcut') {
    if(action === 'New Bill') openApNewBill(data);
    else if(action === 'New Payment') openApNewPayment(data);
    else if(action === 'Vendor Details') openApVendors(data); // Reuses the vendor list
    else if(action === 'New Vendor') openApNewVendor(data);
  }
  else if(type === 'ap-transaction') {
    if(action === 'Bills and Adjustments') openApBills(data);
    else if(action === 'Checks and Payments') openApPayments(data);
  }
  else if(type === 'ap-profile') {
    if(action === 'Vendors') openApVendors(data);
    else if(action === 'Credit Terms') openApCreditTerms(data);
  }
  else if(type === 'ap-process') {
    if(action === 'Release AP Documents') openApProcessReleaseDocs(data);
    else if(action === 'Prepare Payments') openApProcessPreparePayments(data);
    else if(action === 'Process Payments / Print Checks') openApProcessProcessPayments(data);
    else if(action === 'Release Payments') openApProcessReleasePayments(data);
    else if(action === 'Generate Intercompany Documents') openApProcessIntercompany(data);
    else if(action === 'Close Financial Periods') openApProcessClosePeriod(data);
  }
  else if(type === 'ap-inquiry') {
    if(action === 'Vendor Details') openApVendors(data); // Fallback to vendors profile
    else if(action === 'Vendor Summary') openApVendorSummary(data);
  }
  else if(type === 'ap-report') {
    openApReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openApNewBill(data) {
  const vOpts = data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="ab-vnd">${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select id="ab-branch">${bOpts}</select></div>
      <div class="demo-form-row"><label>Bill Date</label><input type="date" id="ab-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Due Date</label><input type="date" id="ab-due" value="2026-06-15"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="ab-ref" placeholder="Vendor Invoice No."></div>
      <div class="demo-form-row"><label>Description</label><input type="text" id="ab-desc" placeholder="E.g. Motorcycle unit purchase"></div>
      <div class="demo-form-row"><label>Expense Account</label><select><option>5000 - Cost of Goods Sold</option><option>6000 - Operating Expenses</option></select></div>
      <div class="demo-form-row"><label>Amount</label><input type="number" id="ab-amt" value="0"></div>
      <div class="demo-form-row"><label>Tax Amount</label><input type="number" id="ab-tax" value="0"></div>
      <div class="demo-form-row"><label>Terms</label><select id="ab-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="ab-rem" placeholder="Optional notes"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitApNewBill(this)">Submit Bill</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>`;
  openCenterModal('New Bill', html, actions);
}

function submitApNewBill(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const no = 'BILL-' + String(data.bills.length + 1001);
    const amt = parseFloat(document.getElementById('ab-amt').value)||0;
    data.bills.unshift({
      no,
      date: document.getElementById('ab-date').value,
      due: document.getElementById('ab-due').value,
      vendor: document.getElementById('ab-vnd').value,
      branch: document.getElementById('ab-branch').value,
      desc: document.getElementById('ab-desc').value || 'AP Bill',
      amount: amt, paid: 0, balance: amt,
      status: 'Open'
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo bill submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openApNewPayment(data) {
  const vOpts = data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const openBills = data.bills.filter(b=>b.status!=='Closed');
  const bOpts = openBills.map(b=>`<option value="${b.no}">${b.no} - &#8369;${fmt(b.balance)} (${b.vendor})</option>`).join('') || '<option value="">No Open Bills</option>';
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor</label><select id="apy-vnd">${vOpts}</select></div>
      <div class="demo-form-row"><label>Bill No.</label><select id="apy-bill" onchange="document.getElementById('apy-amt').value = JSON.parse(localStorage.getItem('payablesDemoData')).bills.find(x=>x.no===this.value)?.balance||0">${bOpts}</select></div>
      <div class="demo-form-row"><label>Payment Date</label><input type="date" id="apy-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Payment Method</label><select id="apy-met"><option>Check</option><option>Bank Transfer</option><option>Cash</option></select></div>
      <div class="demo-form-row"><label>Bank Account</label><select id="apy-bank"><option>BDO-Main</option><option>BPI-Operations</option></select></div>
      <div class="demo-form-row"><label>Reference / Check No.</label><input type="text" id="apy-ref" placeholder="CHK-XXXXX"></div>
      <div class="demo-form-row"><label>Amount Paid</label><input type="number" id="apy-amt" value="${openBills[0]?.balance||0}"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="apy-rem"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitApNewPayment(this)">Save Payment</button><button class="btn btn-sm" onclick="showToast('Voucher printed');">Print Voucher</button>`;
  openCenterModal('New Payment', html, actions);
}

function submitApNewPayment(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const billNo = document.getElementById('apy-bill').value;
    const amt = parseFloat(document.getElementById('apy-amt').value)||0;
    
    data.checksAndPayments.unshift({
      no: 'PAY-' + String(data.checksAndPayments.length + 5001),
      date: document.getElementById('apy-date').value,
      vendor: document.getElementById('apy-vnd').value,
      billNo,
      method: document.getElementById('apy-met').value,
      bank: document.getElementById('apy-bank').value,
      ref: document.getElementById('apy-ref').value,
      amount: amt,
      status: 'Released'
    });
    
    const bill = data.bills.find(b=>b.no===billNo);
    if(bill) {
      bill.paid += amt;
      bill.balance -= amt;
      if(bill.balance <= 0) bill.status = 'Closed';
    }
    
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo vendor payment recorded successfully.');
    closeCenterModal();
  }, 600);
}

function openApNewVendor(data) {
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="nv-name"></div>
      <div class="demo-form-row"><label>Contact Person</label><input type="text" id="nv-contact"></div>
      <div class="demo-form-row"><label>Contact Number</label><input type="text" id="nv-phone"></div>
      <div class="demo-form-row"><label>Email</label><input type="email" id="nv-email"></div>
      <div class="demo-form-row"><label>Address</label><input type="text" id="nv-addr"></div>
      <div class="demo-form-row"><label>Payment Terms</label><select id="nv-terms"><option>NET30</option><option>NET15</option><option>CASH</option></select></div>
      <div class="demo-form-row"><label>Vendor Type</label><select id="nv-type"><option>Manufacturer</option><option>Distributor</option><option>Service</option></select></div>
      <div class="demo-form-row"><label>Tax ID</label><input type="text" id="nv-tax"></div>
      <div class="demo-form-row"><label>Status</label><select id="nv-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitApNewVendor(this)">Save Vendor</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>`;
  openCenterModal('New Vendor', html, actions);
}

function submitApNewVendor(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const id = 'VND-' + String(data.vendors.length + 1).padStart(3, '0');
    data.vendors.push({
      id,
      name: document.getElementById('nv-name').value || 'New Vendor',
      contact: document.getElementById('nv-contact').value,
      phone: document.getElementById('nv-phone').value,
      email: document.getElementById('nv-email').value,
      address: document.getElementById('nv-addr').value,
      terms: document.getElementById('nv-terms').value,
      type: document.getElementById('nv-type').value,
      status: document.getElementById('nv-status').value
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    showToast('Demo vendor saved successfully.');
    // If called from the Vendors modal, it might be nice to refresh, but closeCenterModal is safe.
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function openApBills(data) {
  const rows = data.bills.map(b=>`<tr>
    <td class="mono">${b.no}</td><td class="dim">${b.date}</td><td class="dim">${b.due}</td>
    <td><strong>${b.vendor}</strong></td><td>${b.branch}</td><td>${b.desc}</td>
    <td class="amt">&#8369;${fmt(b.amount)}</td><td class="amt">&#8369;${fmt(b.paid)}</td><td class="amt">&#8369;${fmt(b.balance)}</td>
    <td>${badge(b.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Bill...')">View Bill</button>
      ${b.status==='Open' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updatePayablesLocalData('bills','no','${b.no}',{status:'Ready for Payment'});showToast('Bill marked for payment.');openApBills(JSON.parse(localStorage.getItem('payablesDemoData')))">Mark for Payment</button>` : ''}
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Adjusting Bill...')">Adjust</button>
    </td>
  </tr>`).join('') || '<tr><td colspan="11" style="text-align:center;color:var(--text-tertiary)">No bills</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Bill No.</th><th>Date</th><th>Due Date</th><th>Vendor</th><th>Branch</th><th>Description</th><th>Original Amt</th><th>Paid Amt</th><th>Balance</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('bills.csv', JSON.parse(localStorage.getItem('payablesDemoData')).bills)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Bills and Adjustments', html, actions, {width: 'min(1250px, calc(100vw - 48px))'});
}

function openApPayments(data) {
  const rows = data.checksAndPayments.map(p=>`<tr>
    <td class="mono">${p.no}</td><td class="dim">${p.date}</td><td><strong>${p.vendor}</strong></td>
    <td class="mono">${p.billNo}</td><td>${p.method}</td><td>${p.bank}</td><td class="mono">${p.ref}</td>
    <td class="amt">&#8369;${fmt(p.amount)}</td><td>${badge(p.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Payment')">View Payment</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing Voucher...')">Print Voucher</button>
      ${p.status!=='Voided' ? `<button class="btn btn-sm" style="font-size:10px;padding:2px 6px;color:red" onclick="voidApPayment('${p.no}')">Void Demo Payment</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No payments</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Payment No.</th><th>Date</th><th>Vendor</th><th>Bill No.</th><th>Method</th><th>Bank Account</th><th>Ref/Check No.</th><th>Amount Paid</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('payments.csv', JSON.parse(localStorage.getItem('payablesDemoData')).checksAndPayments)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Checks and Payments', html, actions, {width: '1150px'});
}

function voidApPayment(no) {
  let data = JSON.parse(localStorage.getItem('payablesDemoData'));
  let p = data.checksAndPayments.find(x=>x.no===no);
  if(!p) return;
  p.status = 'Voided';
  let b = data.bills.find(x=>x.no===p.billNo);
  if(b) {
    b.paid -= p.amount;
    b.balance += p.amount;
    if(b.balance > 0 && b.status==='Closed') b.status = 'Open';
  }
  localStorage.setItem('payablesDemoData', JSON.stringify(data));
  showToast('Demo payment voided and balance restored.');
  openApPayments(data);
}

// ======================= PROFILES =======================
function openApVendors(data) {
  const rows = data.vendors.map(v=>{
    const bal = data.bills.filter(b=>b.vendor===v.name).reduce((sum,b)=>sum+b.balance,0);
    return `<tr>
      <td class="mono">${v.id}</td><td><strong>${v.name}</strong></td><td>${v.contact}</td><td class="mono dim">${v.phone}</td>
      <td class="dim">${v.email}</td><td class="mono">${v.terms}</td><td class="amt">${bal>0?('&#8369;'+fmt(bal)):''}</td><td>${badge(v.status)}</td>
      <td style="text-align:center"><button class="btn btn-sm" onclick="openApVendorProfile('${v.id}')" style="font-size:10.5px;padding:3px 10px;min-height:24px">View Profile</button></td>
    </tr>`;
  }).join('');
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Vendor ID</th><th>Vendor Name</th><th>Contact Person</th><th>Contact Number</th><th>Email</th><th>Terms</th><th>Outstanding Bal</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('ap-vendors.csv', JSON.parse(localStorage.getItem('payablesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Vendors', html, actions, {width: '1050px'});
}

function openApVendorProfile(id) {
  const data = JSON.parse(localStorage.getItem('payablesDemoData'));
  const v = data.vendors.find(x=>x.id===id);
  if(!v) return;
  
  const bills = data.bills.filter(b=>b.vendor===v.name);
  const pays = data.checksAndPayments.filter(p=>p.vendor===v.name);
  const totalBills = bills.reduce((sum,b)=>sum+b.amount,0);
  const totalPaid = bills.reduce((sum,b)=>sum+b.paid,0);
  const outstanding = bills.reduce((sum,b)=>sum+b.balance,0);
  
  const billHtml = bills.length ? bills.map(b=>`<tr><td class="mono">${b.no}</td><td class="dim">${b.due}</td><td class="amt">&#8369;${fmt(b.amount)}</td><td class="amt">&#8369;${fmt(b.paid)}</td><td class="amt">&#8369;${fmt(b.balance)}</td><td>${badge(b.status)}</td></tr>`).join('') : '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No bills</td></tr>';
  const payHtml = pays.length ? pays.map(p=>`<tr><td class="mono">${p.no}</td><td class="dim">${p.date}</td><td class="mono">${p.billNo}</td><td>${p.method}</td><td class="mono">${p.ref}</td><td class="amt">&#8369;${fmt(p.amount)}</td><td>${badge(p.status)}</td></tr>`).join('') : '<tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No payments</td></tr>';

  const html = `
    <div class="sp-section" id="ap-vnd-view-mode">
      <div class="sp-section-label">Vendor Information</div>
      <div class="sp-row"><span class="sp-key">Vendor ID</span><span class="sp-val mono">${v.id}</span></div>
      <div class="sp-row"><span class="sp-key">Vendor Name</span><span class="sp-val"><strong>${v.name}</strong></span></div>
      <div class="sp-row"><span class="sp-key">Contact</span><span class="sp-val">${v.contact} - ${v.phone}</span></div>
      <div class="sp-row"><span class="sp-key">Email</span><span class="sp-val">${v.email}</span></div>
      <div class="sp-row"><span class="sp-key">Address</span><span class="sp-val">${v.address||'-'}</span></div>
      <div class="sp-row"><span class="sp-key">Type / Tax ID</span><span class="sp-val">${v.type||'-'} / ${v.tax||'-'}</span></div>
      <div class="sp-row"><span class="sp-key">Payment Terms</span><span class="sp-val mono">${v.terms}</span></div>
      <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(v.status)}</span></div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:15px;background:#f9f9f9;padding:10px;border-radius:6px;border:1px solid var(--border)">
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Total Bills</div><div class="amt" style="font-size:15px">&#8369;${fmt(totalBills)}</div></div>
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Total Paid</div><div class="amt" style="font-size:15px;color:var(--green)">&#8369;${fmt(totalPaid)}</div></div>
        <div style="text-align:center"><div class="dim" style="font-size:11px;text-transform:uppercase">Outstanding Balance</div><div class="amt" style="font-size:15px;color:var(--red)">&#8369;${fmt(outstanding)}</div></div>
      </div>
    </div>
    
    <div class="sp-section" id="ap-vnd-edit-mode" style="display:none">
      <div class="demo-form-grid">
        <div class="demo-form-row"><label>Vendor Name</label><input type="text" id="evap-name" value="${v.name}"></div>
        <div class="demo-form-row"><label>Contact Person</label><input type="text" id="evap-contact" value="${v.contact}"></div>
        <div class="demo-form-row"><label>Contact Number</label><input type="text" id="evap-phone" value="${v.phone}"></div>
        <div class="demo-form-row"><label>Email</label><input type="email" id="evap-email" value="${v.email}"></div>
        <div class="demo-form-row"><label>Payment Terms</label><input type="text" id="evap-terms" value="${v.terms}"></div>
        <div class="demo-form-row"><label>Status</label><select id="evap-status"><option ${v.status==='Active'?'selected':''}>Active</option><option ${v.status==='Inactive'?'selected':''}>Inactive</option></select></div>
        <div class="demo-form-row" style="grid-column:1/-1;display:flex;gap:10px">
          <button class="btn btn-sm btn-primary" onclick="saveApEditVendor('${v.id}')">Save Changes</button>
          <button class="btn btn-sm" onclick="document.getElementById('ap-vnd-edit-mode').style.display='none';document.getElementById('ap-vnd-view-mode').style.display='block';">Cancel</button>
        </div>
      </div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Open Bills</div>
      <div class="table-wrap"><table><thead><tr><th>Bill No.</th><th>Due Date</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead><tbody>${billHtml}</tbody></table></div>
    </div>
    
    <div class="sp-section">
      <div class="sp-section-label">Payment History</div>
      <div class="table-wrap"><table><thead><tr><th>Payment No.</th><th>Date</th><th>Bill No.</th><th>Method</th><th>Ref No.</th><th>Amount</th><th>Status</th></tr></thead><tbody>${payHtml}</tbody></table></div>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm" onclick="document.getElementById('ap-vnd-view-mode').style.display='none';document.getElementById('ap-vnd-edit-mode').style.display='block';">Edit Demo Vendor</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Vendor...');">Export Vendor CSV</button>
    <button class="btn btn-sm" onclick="showToast('Profile printed');setTimeout(()=>window.print(),500)">Print Profile</button>
    <button class="btn btn-sm btn-primary" onclick="openApVendors(JSON.parse(localStorage.getItem('payablesDemoData')))">Close</button>
  `;
  openCenterModal('Vendor Profile: ' + v.name, html, actions, {width: '950px'});
}

function saveApEditVendor(id) {
  updatePayablesLocalData('vendors', 'id', id, {
    name: document.getElementById('evap-name').value,
    contact: document.getElementById('evap-contact').value,
    phone: document.getElementById('evap-phone').value,
    email: document.getElementById('evap-email').value,
    terms: document.getElementById('evap-terms').value,
    status: document.getElementById('evap-status').value
  });
  showToast('Demo vendor updated successfully.');
  openApVendorProfile(id);
}

function openApCreditTerms(data) {
  const rows = data.creditTerms.map(t=>`<tr>
    <td class="mono"><strong>${t.code}</strong></td><td>${t.desc}</td><td class="mono" style="text-align:center">${t.dueDays}</td>
    <td class="mono" style="text-align:center">${t.discount}</td><td>${badge(t.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Editing Term...')">Edit Term</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>Term Code</th><th>Description</th><th style="text-align:center">Due Days</th><th style="text-align:center">Discount</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('credit-terms.csv', JSON.parse(localStorage.getItem('payablesDemoData')).creditTerms)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Credit Terms', html, actions, {width: '800px'});
}

// ======================= PROCESSES =======================
function openApProcessReleaseDocs(data) {
  const bills = data.bills.filter(b=>b.status==='Open');
  const rows = bills.map(b=>`<tr>
    <td class="mono">${b.no}</td><td><strong>${b.vendor}</strong></td><td>Bill</td>
    <td class="amt">&#8369;${fmt(b.amount)}</td><td>${badge('Pending Release')}</td>
  </tr>`).join('') || '<tr><td colspan="5" style="text-align:center;color:var(--text-tertiary)">No pending documents to release</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All Vendors</option></select></div>
      <div class="demo-form-row"><label>Document Status</label><select><option>Unreleased</option></select></div>
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Document No.</th><th>Vendor</th><th>Document Type</th><th>Amount</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApProcessReleaseDocs(this)" ${bills.length===0?'disabled':''}>Release Documents</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Release AP Documents', html, actions, {width: '850px'});
}

function runApProcessReleaseDocs(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    data.bills.filter(b=>b.status==='Open').forEach(b => b.status = 'Released');
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Documents Released';
    btn.disabled = true;
    showToast('Demo AP documents released successfully.');
  }, 1000);
}

function openApProcessPreparePayments(data) {
  // Pull bills that are Open/Released and have balance
  const bills = data.bills.filter(b=>b.balance > 0 && b.status!=='Closed' && b.status!=='Prepared');
  const rows = bills.map(b=>`<tr>
    <td class="mono">${b.no}</td><td><strong>${b.vendor}</strong></td><td class="dim">${b.due}</td>
    <td class="amt">&#8369;${fmt(b.balance)}</td><td>Check</td><td>${badge('Pending')}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No open bills available</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Vendor</label><select><option>All Vendors</option></select></div>
      <div class="demo-form-row"><label>Due Date Cutoff</label><input type="date" value="2026-06-30"></div>
      <div class="demo-form-row"><label>Bank Account</label><select><option>BDO-Main</option><option>BPI-Operations</option></select></div>
      <div class="demo-form-row"><label>Payment Method</label><select><option>Check</option><option>Transfer</option></select></div>
      <div class="demo-form-row"><label>Minimum Amount</label><input type="number" value="0"></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Bill No.</th><th>Vendor</th><th>Due Date</th><th>Balance</th><th>Payment Method</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApProcessPreparePayments(this)" ${bills.length===0?'disabled':''}>Prepare Payment Batch</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Batch...')">Export Batch</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Prepare Payments', html, actions, {width: '900px'});
}

function runApProcessPreparePayments(btn) {
  setButtonLoading(btn, 'Preparing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const bills = data.bills.filter(b=>b.balance > 0 && b.status!=='Closed' && b.status!=='Prepared');
    
    if(bills.length) {
      const batchNo = 'BAT-' + String(data.paymentBatches.length + 100);
      data.paymentBatches.push({ no: batchNo, status: 'Prepared', bills: bills.map(b=>b.no) });
      bills.forEach(b => b.status = 'Prepared');
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
      showToast('Demo payment batch prepared successfully.');
    }
    
    resetButtonLoading(btn);
    btn.textContent = 'Batch Prepared';
    btn.disabled = true;
  }, 1000);
}

function openApProcessProcessPayments(data) {
  const batch = data.paymentBatches.find(b=>b.status==='Prepared');
  let rows = '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No prepared batches found. Run Prepare Payments first.</td></tr>';
  let batchStr = '<option>None</option>';
  if(batch) {
    batchStr = `<option>${batch.no}</option>`;
    const batchBills = data.bills.filter(b=>batch.bills.includes(b.no));
    rows = batchBills.map((b,i)=>`<tr>
      <td class="mono">PAY-${5100+i}</td><td><strong>${b.vendor}</strong></td><td class="mono">${b.no}</td>
      <td class="amt">&#8369;${fmt(b.balance)}</td><td class="mono">CHK-${10020+i}</td><td>${badge('Prepared')}</td>
    </tr>`).join('');
  }
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Payment Batch</label><select>${batchStr}</select></div>
      <div class="demo-form-row"><label>Bank Account</label><select><option>BDO-Main</option></select></div>
      <div class="demo-form-row"><label>Check Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Starting Check No.</label><input type="text" value="CHK-10020"></div>
      <div class="demo-form-row"><label>Print Option</label><select><option>Print Checks</option><option>Export PDF File</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Payment No.</th><th>Vendor</th><th>Bill No.</th><th>Amount</th><th>Check No.</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApProcessProcessPayments(this)" ${!batch?'disabled':''}>Process Payments</button>
    <button class="btn btn-sm" onclick="showToast('Checks sent to printer.')" ${!batch?'disabled':''}>Print Checks</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Process Payments / Print Checks', html, actions, {width: '900px'});
}

function runApProcessProcessPayments(btn) {
  setButtonLoading(btn, 'Processing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const batch = data.paymentBatches.find(b=>b.status==='Prepared');
    if(batch) {
      batch.status = 'Processed';
      const batchBills = data.bills.filter(b=>batch.bills.includes(b.no));
      batchBills.forEach((b,i) => {
        data.checksAndPayments.unshift({
          no: 'PAY-' + String(5100+i),
          date: '2026-05-16',
          vendor: b.vendor,
          billNo: b.no,
          method: 'Check',
          bank: 'BDO-Main',
          ref: 'CHK-' + String(10020+i),
          amount: b.balance,
          status: 'Processed'
        });
        b.status = 'Payment Processed';
      });
      localStorage.setItem('payablesDemoData', JSON.stringify(data));
      showToast('Demo payments processed successfully.');
    }
    resetButtonLoading(btn);
    btn.textContent = 'Payments Processed';
    btn.disabled = true;
  }, 1200);
}

function openApProcessReleasePayments(data) {
  const processed = data.checksAndPayments.filter(p=>p.status==='Processed');
  const rows = processed.map(p=>`<tr>
    <td class="mono">${p.no}</td><td><strong>${p.vendor}</strong></td><td class="amt">&#8369;${fmt(p.amount)}</td>
    <td class="mono">${p.billNo}</td><td>${badge('Processed')}</td>
  </tr>`).join('') || '<tr><td colspan="5" style="text-align:center;color:var(--text-tertiary)">No processed payments waiting for release.</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Payment Batch</label><select><option>All Unreleased</option></select></div>
      <div class="demo-form-row"><label>Vendor</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Payment Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Status</label><select><option>Processed</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:300px;overflow-y:auto">
      <table><thead><tr><th>Payment No.</th><th>Vendor</th><th>Amount</th><th>Bill No.</th><th>Release Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApProcessReleasePayments(this)" ${processed.length===0?'disabled':''}>Release Payments</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Release Payments', html, actions, {width: '850px'});
}

function runApProcessReleasePayments(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    const processed = data.checksAndPayments.filter(p=>p.status==='Processed');
    processed.forEach(p => {
      p.status = 'Released';
      const b = data.bills.find(x=>x.no===p.billNo);
      if(b) {
        b.paid += p.amount;
        b.balance -= p.amount;
        if(b.balance <= 0) b.status = 'Closed';
      }
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Payments Released';
    btn.disabled = true;
    showToast('Demo payments released successfully.');
  }, 1000);
}

function openApProcessIntercompany(data) {
  const bOpts = branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  const rows = data.intercompanyDocuments.map(d=>`<tr>
    <td class="mono">${d.no}</td><td>${d.source}</td><td>${d.dest}</td><td>${d.vendor}</td>
    <td class="amt">&#8369;${fmt(d.amt)}</td><td>${badge(d.status)}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No intercompany documents</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Source Branch</label><select id="api-src">${bOpts}</select></div>
      <div class="demo-form-row"><label>Destination Branch</label><select id="api-dest">${bOpts}</select></div>
      <div class="demo-form-row"><label>Vendor / Internal</label><select id="api-vnd"><option>NEXII HQ</option><option>Interbranch Logistics</option></select></div>
      <div class="demo-form-row"><label>Transaction Date</label><input type="date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Reason</label><input type="text" id="api-rsn" placeholder="Cost allocation"></div>
      <div class="demo-form-row"><label>Amount</label><input type="number" id="api-amt" value="25000"></div>
    </div>
    <div class="table-wrap"><table><thead><tr><th>Intercompany Doc No.</th><th>Source Branch</th><th>Destination Branch</th><th>Vendor</th><th>Amount</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table></div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApIntercompany(this)">Generate Intercompany Documents</button>
    <button class="btn btn-sm" onclick="showToast('Exporting Log...')">Export Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Generate Intercompany Documents', html, actions, {width: '900px'});
}

function runApIntercompany(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('payablesDemoData'));
    data.intercompanyDocuments.unshift({
      no: 'ICD-' + String(data.intercompanyDocuments.length + 100),
      source: document.getElementById('api-src').value,
      dest: document.getElementById('api-dest').value,
      vendor: document.getElementById('api-vnd').value,
      amt: parseFloat(document.getElementById('api-amt').value)||0,
      status: 'Generated'
    });
    localStorage.setItem('payablesDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    showToast('Demo intercompany AP documents generated successfully.');
    openApProcessIntercompany(data);
  }, 800);
}

function openApProcessClosePeriod(data) {
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Financial Period</label><select><option>05-2026</option></select></div>
      <div class="demo-form-row"><label>Branch</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Module</label><select><option>Accounts Payable</option></select></div>
      <div class="demo-form-row"><label>Closing Option</label><select><option>Soft Close</option><option>Hard Close</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table>
        <thead><tr><th>Validation Item</th><th>Result</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>Unreleased AP documents</td><td class="dim" id="val-1">-</td><td id="stat-1">${badge('Pending')}</td></tr>
          <tr><td>Unprocessed payments</td><td class="dim" id="val-2">-</td><td id="stat-2">${badge('Pending')}</td></tr>
          <tr><td>Open payment batches</td><td class="dim" id="val-3">-</td><td id="stat-3">${badge('Pending')}</td></tr>
          <tr><td>Vendor balance validation</td><td class="dim" id="val-4">-</td><td id="stat-4">${badge('Pending')}</td></tr>
          <tr><td>GL posting check</td><td class="dim" id="val-5">-</td><td id="stat-5">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runApValidatePeriod(this)" id="ap-val-btn">Validate Period</button>
    <button class="btn btn-sm btn-primary" onclick="runApClosePeriod(this)" id="ap-clo-btn" disabled>Close Period</button>
    <button class="btn btn-sm" onclick="showToast('Printing Log...')">Print Closing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Close Financial Periods', html, actions, {width: '800px'});
}

function runApValidatePeriod(btn) {
  setButtonLoading(btn, 'Validating...');
  setTimeout(() => {
    document.getElementById('val-1').textContent = '0 Documents'; document.getElementById('stat-1').innerHTML = badge('OK');
    document.getElementById('val-2').textContent = '0 Payments'; document.getElementById('stat-2').innerHTML = badge('OK');
    document.getElementById('val-3').textContent = 'None'; document.getElementById('stat-3').innerHTML = badge('OK');
    document.getElementById('val-4').textContent = 'Balanced'; document.getElementById('stat-4').innerHTML = badge('OK');
    document.getElementById('val-5').textContent = 'Posted'; document.getElementById('stat-5').innerHTML = badge('OK');
    resetButtonLoading(btn);
    btn.textContent = 'Validated';
    btn.disabled = true;
    document.getElementById('ap-clo-btn').disabled = false;
    showToast('Period validation complete. Ready to close.');
  }, 1500);
}

function runApClosePeriod(btn) {
  setButtonLoading(btn, 'Closing Period...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Period Closed';
    btn.disabled = true;
    showToast('Demo AP financial period closed successfully.');
  }, 1000);
}

// ======================= INQUIRIES =======================
function openApVendorSummary(data) {
  const rows = data.vendors.map(v=>{
    const bills = data.bills.filter(b=>b.vendor===v.name);
    const tb = bills.reduce((sum,b)=>sum+b.amount,0);
    const tp = bills.reduce((sum,b)=>sum+b.paid,0);
    const ob = bills.reduce((sum,b)=>sum+b.balance,0);
    return `<tr>
      <td><strong>${v.name}</strong></td><td class="amt">&#8369;${fmt(tb)}</td><td class="amt">&#8369;${fmt(tp)}</td>
      <td class="amt">&#8369;${fmt(ob)}</td><td class="amt">&#8369;${fmt(ob*0.4)}</td><td class="amt">&#8369;${fmt(ob*0.3)}</td>
      <td class="amt">&#8369;${fmt(ob*0.2)}</td><td class="amt">&#8369;${fmt(ob*0.1)}</td><td>${badge(v.status)}</td>
    </tr>`;
  }).join('');
  
  const html = `
    <div style="margin-bottom:12px;display:flex;gap:10px">
      <input type="text" placeholder="Search Vendor..." style="padding:6px 10px;border:1px solid var(--border);border-radius:4px;flex:1">
      <button class="btn btn-sm">Search</button>
    </div>
    <div class="table-wrap"><table>
    <thead><tr><th>Vendor</th><th>Total Bills</th><th>Total Paid</th><th>Outstanding Bal</th><th>Current</th><th>1-30 Days</th><th>31-60 Days</th><th>61+ Days</th><th>Status</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('vendor-summary.csv', JSON.parse(localStorage.getItem('payablesDemoData')).vendors)">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Summary...')">Print Summary</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Vendor Summary', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

// ======================= REPORTS =======================
function openApReportModal(reportName, data) {
  const vOpts = `<option value="">All Vendors</option>` + data.vendors.map(v=>`<option value="${v.name}">${v.name}</option>`).join('');
  const bOpts = `<option value="">All Branches</option>` + branchData.slice(0,10).map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Vendor</label><select>${vOpts}</select></div>
      <div class="demo-form-row"><label>Branch</label><select>${bOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option><option>Open</option><option>Closed</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runApDemoReport(this, '${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="ap-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1050px, calc(100vw - 48px))'});
}

function runApDemoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('payablesDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'AP Balance by GL Account') {
      thead = `<tr><th>GL Account</th><th>Account Name</th><th style="text-align:center">Vendor Count</th><th style="text-align:center">Bill Count</th><th>Total Balance</th><th>Status</th></tr>`;
      rows = `<tr><td class="mono">20000</td><td>Accounts Payable - Trade</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">12</td><td class="amt">&#8369;1,250,000</td><td>${badge('Active')}</td></tr>
              <tr><td class="mono">20100</td><td>Accounts Payable - Non-Trade</td><td class="mono" style="text-align:center">3</td><td class="mono" style="text-align:center">4</td><td class="amt">&#8369;45,000</td><td>${badge('Active')}</td></tr>`;
    } else if (reportName === 'AP Balance by Vendor') {
      thead = `<tr><th>Vendor</th><th style="text-align:center">Total Bills</th><th>Total Paid</th><th>Outstanding Balance</th><th>Status</th></tr>`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const tb = bills.reduce((sum,b)=>sum+b.amount,0);
        const tp = bills.reduce((sum,b)=>sum+b.paid,0);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(tb===0) return '';
        return `<tr><td><strong>${v.name}</strong></td><td class="mono" style="text-align:center">${bills.length}</td><td class="amt">&#8369;${fmt(tp)}</td><td class="amt">&#8369;${fmt(ob)}</td><td>${badge('Active')}</td></tr>`;
      }).join('');
    } else if (reportName === 'AP Aging') {
      thead = `<tr><th>Vendor</th><th>Current</th><th>1-30 Days</th><th>31-60 Days</th><th>61-90 Days</th><th>Over 90 Days</th><th>Total Balance</th></tr>`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(ob===0) return '';
        return `<tr><td><strong>${v.name}</strong></td><td class="amt">&#8369;${fmt(ob*0.4)}</td><td class="amt">&#8369;${fmt(ob*0.3)}</td><td class="amt">&#8369;${fmt(ob*0.2)}</td><td class="amt">&#8369;${fmt(ob*0.1)}</td><td class="amt">&#8369;0</td><td class="amt" style="font-weight:bold">&#8369;${fmt(ob)}</td></tr>`;
      }).join('');
    } else if (reportName === 'AP Aged Period Sensitive') {
      thead = `<tr><th>Vendor</th><th>Financial Period</th><th>Opening Balance</th><th>New Bills</th><th>Payments</th><th>Ending Balance</th><th>Aging Status</th></tr>`;
      rows = data.vendors.map(v=>{
        const bills = data.bills.filter(b=>b.vendor===v.name);
        const tb = bills.reduce((sum,b)=>sum+b.amount,0);
        const tp = bills.reduce((sum,b)=>sum+b.paid,0);
        const ob = bills.reduce((sum,b)=>sum+b.balance,0);
        if(tb===0) return '';
        return `<tr><td><strong>${v.name}</strong></td><td class="mono">05-2026</td><td class="amt">&#8369;0</td><td class="amt">&#8369;${fmt(tb)}</td><td class="amt">&#8369;${fmt(tp)}</td><td class="amt" style="font-weight:bold">&#8369;${fmt(ob)}</td><td>${badge('Current')}</td></tr>`;
      }).join('');
    } else {
      thead = `<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>`;
      rows = `<tr><td class="mono">REC-001</td><td class="dim">2026-05-16</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>${badge('Active')}</td></tr>`;
    }
    
    document.getElementById('ap-report-body').innerHTML = `<table><thead>${thead}</thead><tbody>${rows}</tbody></table>`;
    resetButtonLoading(btn);
    showToast('Demo AP report generated successfully.');
  }, 800);
}
// --- END PAYABLES MODULE DEMO ---

// --- INVENTORY MODULE FULL DEMO ---
function initInventoryDemoData() {
  let d = JSON.parse(localStorage.getItem('inventoryDemoData'));
  if(!d) {
    d = {
      stockItems: [
        { code: 'ITM-001', desc: 'Honda Click 125i', class: 'Motor Unit', brand: 'Honda', uom: 'Unit', cost: 65000, price: 81400, qty: 15, reorder: 5, status: 'Active' },
        { code: 'ITM-002', desc: 'Honda ADV 160', class: 'Motor Unit', brand: 'Honda', uom: 'Unit', cost: 130000, price: 166900, qty: 8, reorder: 3, status: 'Active' },
        { code: 'ITM-003', desc: 'Yamaha Mio Gear', class: 'Motor Unit', brand: 'Yamaha', uom: 'Unit', cost: 60000, price: 77400, qty: 20, reorder: 5, status: 'Active' },
        { code: 'ITM-004', desc: 'Helmet Standard', class: 'Accessories', brand: 'Generic', uom: 'Pcs', cost: 800, price: 1500, qty: 100, reorder: 20, status: 'Active' },
        { code: 'ITM-005', desc: 'Engine Oil 10W-40', class: 'Consumables', brand: 'Generic', uom: 'Liters', cost: 200, price: 350, qty: 250, reorder: 50, status: 'Active' },
        { code: 'ITM-006', desc: 'Spark Plug', class: 'Spare Parts', brand: 'NGK', uom: 'Pcs', cost: 150, price: 250, qty: 500, reorder: 100, status: 'Active' }
      ],
      nonStockItems: [
        { code: 'NS-001', desc: 'Delivery Charge', cat: 'Service', account: '4001', cost: 500, status: 'Active' },
        { code: 'NS-002', desc: 'Warranty Labor', cat: 'Labor', account: '6001', cost: 0, status: 'Active' }
      ],
      warehouses: [
        { id: 'WH-MNL', name: 'Manila Main Warehouse', branch: 'Manila Main', loc: 'Metro Manila', manager: 'Admin', cap: 500, util: 60, status: 'Active' },
        { id: 'WH-QC', name: 'Quezon City Warehouse', branch: 'Quezon City North', loc: 'Quezon City', manager: 'Admin', cap: 300, util: 45, status: 'Active' },
        { id: 'WH-CEB', name: 'Cebu Warehouse', branch: 'Cebu Branch', loc: 'Cebu', manager: 'Admin', cap: 400, util: 20, status: 'Active' }
      ],
      warehouseBuildings: [
        { id: 'BLDG-A', name: 'Main Assembly Hall', wh: 'WH-MNL', loc: 'Compound A', area: '1000 sqm', zones: 4, status: 'Active' }
      ],
      receipts: [
        { no: 'REC-2001', date: '2026-05-10', wh: 'WH-MNL', source: 'Honda Philippines', item: 'Honda Click 125i', qty: 5, status: 'Released' }
      ],
      issues: [
        { no: 'ISS-3001', date: '2026-05-12', wh: 'WH-QC', to: 'Sales Dept', item: 'Helmet Standard', qty: 2, reason: 'Demo Unit', status: 'Released' }
      ],
      adjustments: [],
      transfers: [
        { no: 'TRF-4001', date: '2026-05-14', from: 'WH-MNL', to: 'WH-QC', item: 'Engine Oil 10W-40', qty: 50, status: 'In Transit' }
      ],
      kitAssemblies: [],
      physicalCounts: []
    };
    localStorage.setItem('inventoryDemoData', JSON.stringify(d));
  }
  return d;
}

function updateInventoryLocalData(key, idField, idValue, newProps) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  let arr = data[key];
  if(arr) {
    let obj = arr.find(x => x[idField] === idValue);
    if(obj) {
      Object.assign(obj, newProps);
      localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    }
  }
}

function openInventoryDemo(action, type) {
  const data = initInventoryDemoData();
  
  if(type === 'in-shortcut') {
    if(action === 'New Adjustment') openInNewAdjustment(data);
    else if(action === 'New Transfer') openInNewTransfer(data);
    else if(action === 'New Kit Assembly') openInNewKitAssembly(data);
    else if(action === 'New Stock Item') openInNewStockItem(data);
  }
  else if(type === 'in-transaction') {
    if(action === 'Receipts') openInReceipts(data);
    else if(action === 'Issues') openInIssues(data);
    else if(action === 'Adjustments') openInAdjustments(data);
    else if(action === 'Transfers') openInTransfers(data);
    else if(action === 'Kit Assembly') openInKitAssembly(data);
  }
  else if(type === 'in-profile') {
    if(action === 'Stock Items') openInStockItems(data);
    else if(action === 'Item Warehouse Details') openInWarehouseDetails(data);
    else if(action === 'Non-Stock Items') openInNonStockItems(data);
    else if(action === 'Warehouses') openInWarehouses(data);
    else if(action === 'Warehouse Buildings') openInBuildings(data);
  }
  else if(type === 'in-physical') {
    if(action === 'Prepare Physical Count') openInPrepareCount(data);
    else if(action === 'Physical Inventory Count') openInPhysicalCount(data);
  }
  else if(type === 'in-process') {
    if(action === 'Release IN Documents') openInProcessReleaseDocs(data);
    else if(action === 'Close Financial Periods') openInProcessClosePeriod(data);
  }
  else if(type === 'in-inquiry') {
    openInInquiryModal(action, data);
  }
  else if(type === 'in-report') {
    openInReportModal(action, data);
  }
}

// ======================= SHORTCUTS =======================
function openInNewAdjustment(data) {
  const whOpts = data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  const itemOpts = data.stockItems.map(i=>`<option value="${i.code}">${i.code} - ${i.desc}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Adjustment Date</label><input type="date" id="na-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Warehouse</label><select id="na-wh">${whOpts}</select></div>
      <div class="demo-form-row"><label>Item / Unit Model</label><select id="na-item">${itemOpts}</select></div>
      <div class="demo-form-row"><label>Lot / Serial No.</label><input type="text" id="na-lot" placeholder="Optional"></div>
      <div class="demo-form-row"><label>Adjustment Type</label><select id="na-type"><option>Increase</option><option>Decrease</option><option>Reclassification</option><option>Damage</option><option>Correction</option></select></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="na-qty" value="1"></div>
      <div class="demo-form-row"><label>Reason</label><input type="text" id="na-rsn"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="na-ref"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="na-rem"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitInAdjustment(this)">Submit Adjustment</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>`;
  openCenterModal('New Adjustment', html, actions);
}

function submitInAdjustment(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const itemCode = document.getElementById('na-item').value;
    const type = document.getElementById('na-type').value;
    const qty = parseInt(document.getElementById('na-qty').value)||0;
    
    // Create adjustment
    data.adjustments.unshift({
      no: 'ADJ-' + String(data.adjustments.length + 1001),
      date: document.getElementById('na-date').value,
      wh: document.getElementById('na-wh').value,
      item: itemCode,
      lot: document.getElementById('na-lot').value,
      type, qty,
      reason: document.getElementById('na-rsn').value,
      status: 'Released'
    });
    
    // Update stock quantity directly because we are releasing it instantly in this shortcut
    let item = data.stockItems.find(i=>i.code===itemCode);
    if(item) {
      if(type==='Increase' || type==='Correction') item.qty += qty;
      else if(type==='Decrease' || type==='Damage') item.qty = Math.max(0, item.qty - qty);
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo inventory adjustment submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openInNewTransfer(data) {
  const whOpts = data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  const itemOpts = data.stockItems.map(i=>`<option value="${i.code}">${i.code} - ${i.desc}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Transfer Date</label><input type="date" id="nt-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>From Warehouse</label><select id="nt-from">${whOpts}</select></div>
      <div class="demo-form-row"><label>To Warehouse</label><select id="nt-to">${whOpts}</select></div>
      <div class="demo-form-row"><label>Item</label><select id="nt-item">${itemOpts}</select></div>
      <div class="demo-form-row"><label>Lot / Serial No.</label><input type="text" id="nt-lot"></div>
      <div class="demo-form-row"><label>Quantity</label><input type="number" id="nt-qty" value="1"></div>
      <div class="demo-form-row"><label>Transfer Reason</label><input type="text" id="nt-rsn" value="Stock Balancing"></div>
      <div class="demo-form-row"><label>Requested By</label><input type="text" id="nt-req" value="System Admin"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Remarks</label><input type="text" id="nt-rem"></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitInTransfer(this)">Submit Transfer</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>`;
  openCenterModal('New Transfer', html, actions);
}

function submitInTransfer(btn) {
  setButtonLoading(btn, 'Submitting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const itemCode = document.getElementById('nt-item').value;
    const qty = parseInt(document.getElementById('nt-qty').value)||0;
    
    data.transfers.unshift({
      no: 'TRF-' + String(data.transfers.length + 4001),
      date: document.getElementById('nt-date').value,
      from: document.getElementById('nt-from').value,
      to: document.getElementById('nt-to').value,
      item: itemCode,
      lot: document.getElementById('nt-lot').value,
      qty,
      status: 'In Transit' // deduct source immediately
    });
    
    let item = data.stockItems.find(i=>i.code===itemCode);
    if(item) {
      item.qty = Math.max(0, item.qty - qty); // deduct from global available (for demo simplicity)
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo inventory transfer submitted successfully.');
    closeCenterModal();
  }, 600);
}

function openInNewKitAssembly(data) {
  const whOpts = data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Assembly Date</label><input type="date" id="nk-date" value="2026-05-16"></div>
      <div class="demo-form-row"><label>Warehouse</label><select id="nk-wh">${whOpts}</select></div>
      <div class="demo-form-row"><label>Kit Item</label><input type="text" id="nk-item" value="Motorcycle Starter Kit" readonly style="background:#f5f5f5"></div>
      <div class="demo-form-row"><label>Quantity to Assemble</label><input type="number" id="nk-qty" value="5"></div>
      <div class="demo-form-row"><label>Reference No.</label><input type="text" id="nk-ref"></div>
      <div class="demo-form-row"><label>Remarks</label><input type="text" id="nk-rem"></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Component Item</th><th>Qty per Kit</th><th>Total Required</th><th>Available</th></tr></thead>
        <tbody>
          <tr><td>Helmet Standard</td><td class="mono" style="text-align:center">1</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">100</td></tr>
          <tr><td>Engine Oil 10W-40</td><td class="mono" style="text-align:center">2</td><td class="mono" style="text-align:center">10</td><td class="mono" style="text-align:center">250</td></tr>
          <tr><td>Spark Plug</td><td class="mono" style="text-align:center">1</td><td class="mono" style="text-align:center">5</td><td class="mono" style="text-align:center">500</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitInKitAssembly(this)">Assemble Kit</button><button class="btn btn-sm" onclick="closeCenterModal()">Save Draft</button>`;
  openCenterModal('New Kit Assembly', html, actions, {width: '800px'});
}

function submitInKitAssembly(btn) {
  setButtonLoading(btn, 'Assembling...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const qty = parseInt(document.getElementById('nk-qty').value)||0;
    
    data.kitAssemblies.unshift({
      no: 'KIT-' + String(data.kitAssemblies.length + 101),
      date: document.getElementById('nk-date').value,
      wh: document.getElementById('nk-wh').value,
      item: 'Motorcycle Starter Kit',
      qty,
      comps: `Helmet(${1*qty}), Oil(${2*qty}), Plug(${1*qty})`,
      status: 'Completed'
    });
    
    // Deduct components
    let helm = data.stockItems.find(i=>i.code==='ITM-004'); if(helm) helm.qty = Math.max(0, helm.qty - qty);
    let oil = data.stockItems.find(i=>i.code==='ITM-005'); if(oil) oil.qty = Math.max(0, oil.qty - (qty*2));
    let plug = data.stockItems.find(i=>i.code==='ITM-006'); if(plug) plug.qty = Math.max(0, plug.qty - qty);
    
    // Add Kit as stock item if not exist
    let kit = data.stockItems.find(i=>i.code==='ITM-KIT1');
    if(!kit) {
      data.stockItems.push({ code: 'ITM-KIT1', desc: 'Motorcycle Starter Kit', class: 'Accessories', brand: 'NEXII', uom: 'Kit', cost: 1350, price: 2000, qty: qty, reorder: 5, status: 'Active' });
    } else {
      kit.qty += qty;
    }
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo kit assembly completed successfully.');
    closeCenterModal();
  }, 800);
}

function openInNewStockItem(data) {
  const whOpts = data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  const html = `
    <div class="demo-form-grid">
      <div class="demo-form-row"><label>Item Code</label><input type="text" id="ni-code" value="ITM-00X"></div>
      <div class="demo-form-row"><label>Item Description</label><input type="text" id="ni-desc"></div>
      <div class="demo-form-row"><label>Item Class</label><select id="ni-class"><option>Motor Unit</option><option>Spare Parts</option><option>Accessories</option><option>Consumables</option></select></div>
      <div class="demo-form-row"><label>Brand</label><input type="text" id="ni-brand"></div>
      <div class="demo-form-row"><label>Unit of Measure</label><select id="ni-uom"><option>Unit</option><option>Pcs</option><option>Liters</option><option>Box</option></select></div>
      <div class="demo-form-row"><label>Standard Cost</label><input type="number" id="ni-cost" value="0"></div>
      <div class="demo-form-row"><label>Sales Price</label><input type="number" id="ni-price" value="0"></div>
      <div class="demo-form-row"><label>Reorder Point</label><input type="number" id="ni-reorder" value="5"></div>
      <div class="demo-form-row"><label>Warehouse</label><select>${whOpts}</select></div>
      <div class="demo-form-row"><label>Initial Quantity</label><input type="number" id="ni-qty" value="0"></div>
      <div class="demo-form-row"><label>Lot/Serial Tracking</label><select><option>Not Tracked</option><option>Lot Tracked</option><option>Serial Tracked</option></select></div>
      <div class="demo-form-row"><label>Status</label><select id="ni-status"><option>Active</option><option>Inactive</option></select></div>
    </div>
  `;
  const actions = `<button class="btn btn-sm btn-primary" onclick="submitInStockItem(this)">Save Stock Item</button><button class="btn btn-sm" onclick="closeCenterModal()">Cancel</button>`;
  openCenterModal('New Stock Item', html, actions);
}

function submitInStockItem(btn) {
  setButtonLoading(btn, 'Saving...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    data.stockItems.push({
      code: document.getElementById('ni-code').value || 'ITM-NEW',
      desc: document.getElementById('ni-desc').value || 'New Demo Item',
      class: document.getElementById('ni-class').value,
      brand: document.getElementById('ni-brand').value || 'Generic',
      uom: document.getElementById('ni-uom').value,
      cost: parseFloat(document.getElementById('ni-cost').value)||0,
      price: parseFloat(document.getElementById('ni-price').value)||0,
      qty: parseInt(document.getElementById('ni-qty').value)||0,
      reorder: parseInt(document.getElementById('ni-reorder').value)||0,
      status: document.getElementById('ni-status').value
    });
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo stock item created successfully.');
    closeCenterModal();
  }, 600);
}

// ======================= TRANSACTIONS =======================
function openInReceipts(data) {
  const rows = data.receipts.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.wh}</td><td>${r.source}</td>
    <td>${r.item}</td><td class="dim">-</td><td class="mono" style="text-align:center">${r.qty}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Receipt...')">View Receipt</button>
      ${r.status==='Pending' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('receipts','no','${r.no}',{status:'Released'});showToast('Receipt Released.');openInReceipts(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release Receipt</button>` : ''}
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing...')">Print</button>
    </td>
  </tr>`).join('') || '<tr><td colspan="9" style="text-align:center;color:var(--text-tertiary)">No receipts</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Receipt No.</th><th>Date</th><th>Warehouse</th><th>Source</th><th>Item</th><th>Lot/Serial No.</th><th style="text-align:center">Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-receipts.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).receipts)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Inventory Receipts', html, actions, {width: 'min(1100px, calc(100vw - 48px))'});
}

function openInIssues(data) {
  const rows = data.issues.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.wh}</td><td>${r.to}</td>
    <td>${r.item}</td><td class="dim">-</td><td class="mono" style="text-align:center">${r.qty}</td><td>${r.reason}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Issue...')">View Issue</button>
      ${r.status==='Pending' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('issues','no','${r.no}',{status:'Released'});showToast('Issue Released.');openInIssues(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release Issue</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No issues</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Issue No.</th><th>Date</th><th>Warehouse</th><th>Issued To</th><th>Item</th><th>Lot/Serial No.</th><th style="text-align:center">Qty</th><th>Reason</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-issues.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).issues)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Inventory Issues', html, actions, {width: 'min(1150px, calc(100vw - 48px))'});
}

function openInAdjustments(data) {
  const rows = data.adjustments.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.wh}</td><td>${r.item}</td>
    <td class="dim">${r.lot||'-'}</td><td>${r.type}</td><td class="mono" style="text-align:center">${r.qty}</td><td>${r.reason}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Adj...')">View</button>
      ${r.status==='Pending' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('adjustments','no','${r.no}',{status:'Released'});showToast('Adj Released.');openInAdjustments(JSON.parse(localStorage.getItem('inventoryDemoData')))">Release</button>` : ''}
      ${r.status==='Released' ? `<button class="btn btn-sm" style="font-size:10px;padding:2px 6px;color:red" onclick="updateInventoryLocalData('adjustments','no','${r.no}',{status:'Reversed'});showToast('Reversed.');openInAdjustments(JSON.parse(localStorage.getItem('inventoryDemoData')))">Reverse</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="10" style="text-align:center;color:var(--text-tertiary)">No adjustments</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Adj No.</th><th>Date</th><th>Warehouse</th><th>Item</th><th>Lot/Serial</th><th>Type</th><th style="text-align:center">Qty</th><th>Reason</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-adjustments.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).adjustments)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Inventory Adjustments', html, actions, {width: 'min(1200px, calc(100vw - 48px))'});
}

function openInTransfers(data) {
  const rows = data.transfers.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.from}</td><td>${r.to}</td>
    <td>${r.item}</td><td class="mono" style="text-align:center">${r.qty}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Transfer...')">View Transfer</button>
      ${r.status==='Pending' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="updateInventoryLocalData('transfers','no','${r.no}',{status:'In Transit'});showToast('Shipped.');openInTransfers(JSON.parse(localStorage.getItem('inventoryDemoData')))">Ship Transfer</button>` : ''}
      ${r.status==='In Transit' ? `<button class="btn btn-sm btn-primary" style="font-size:10px;padding:2px 6px" onclick="receiveInTransfer('${r.no}')">Receive Transfer</button>` : ''}
    </td>
  </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No transfers</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Transfer No.</th><th>Date</th><th>From Whse</th><th>To Whse</th><th>Item</th><th style="text-align:center">Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-transfers.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).transfers)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Inventory Transfers', html, actions, {width: '1000px'});
}

function receiveInTransfer(no) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  let trf = data.transfers.find(t=>t.no===no);
  if(trf) {
    trf.status = 'Received';
    let item = data.stockItems.find(i=>i.code===trf.item);
    if(item) item.qty += trf.qty; // add to dest (for demo global pool)
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Transfer received successfully.');
    openInTransfers(data);
  }
}

function openInKitAssembly(data) {
  const rows = data.kitAssemblies.map(r=>`<tr>
    <td class="mono">${r.no}</td><td class="dim">${r.date}</td><td>${r.wh}</td><td>${r.item}</td>
    <td class="mono" style="text-align:center">${r.qty}</td><td class="dim" style="font-size:11px">${r.comps}</td><td>${badge(r.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing Assembly...')">View</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Printing Slip...')">Print</button>
    </td>
  </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No kit assemblies</td></tr>';
  
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Assembly No.</th><th>Date</th><th>Warehouse</th><th>Kit Item</th><th style="text-align:center">Qty</th><th>Components</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-assemblies.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).kitAssemblies)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Kit Assembly', html, actions, {width: '1050px'});
}

// ======================= PROFILES =======================
function openInStockItems(data) {
  const rows = data.stockItems.map(i=>`<tr>
    <td class="mono">${i.code}</td><td><strong>${i.desc}</strong></td><td>${i.class}</td><td>${i.brand}</td>
    <td class="mono">${i.uom}</td><td class="amt">&#8369;${fmt(i.cost)}</td><td class="amt">&#8369;${fmt(i.price)}</td>
    <td class="mono" style="text-align:center">${i.qty}</td><td>${badge(i.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="openStockItemProfile('${i.code}')">View Profile</button>
      <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Edit item...')">Edit</button>
    </td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Item Code</th><th>Description</th><th>Item Class</th><th>Brand</th><th>UOM</th><th>Std Cost</th><th>Sales Price</th><th style="text-align:center">Total Qty</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('stock-items.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).stockItems)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Stock Items', html, actions, {width: 'min(1250px, calc(100vw - 48px))'});
}

function openStockItemProfile(code) {
  const data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  const i = data.stockItems.find(x=>x.code===code);
  if(!i) return;
  
  const val = i.qty * i.cost;
  
  const html = `
    <div class="sp-section">
      <div class="sp-section-label">Item Information</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div>
          <div class="sp-row"><span class="sp-key">Item Code</span><span class="sp-val mono">${i.code}</span></div>
          <div class="sp-row"><span class="sp-key">Description</span><span class="sp-val"><strong>${i.desc}</strong></span></div>
          <div class="sp-row"><span class="sp-key">Item Class</span><span class="sp-val">${i.class}</span></div>
          <div class="sp-row"><span class="sp-key">Brand</span><span class="sp-val">${i.brand}</span></div>
          <div class="sp-row"><span class="sp-key">UOM</span><span class="sp-val mono">${i.uom}</span></div>
        </div>
        <div>
          <div class="sp-row"><span class="sp-key">Standard Cost</span><span class="sp-val amt">&#8369;${fmt(i.cost)}</span></div>
          <div class="sp-row"><span class="sp-key">Sales Price</span><span class="sp-val amt">&#8369;${fmt(i.price)}</span></div>
          <div class="sp-row"><span class="sp-key">Total Qty</span><span class="sp-val mono">${i.qty}</span></div>
          <div class="sp-row"><span class="sp-key">Reorder Point</span><span class="sp-val mono">${i.reorder}</span></div>
          <div class="sp-row"><span class="sp-key">Status</span><span class="sp-val">${badge(i.status)}</span></div>
        </div>
      </div>
      <div style="margin-top:15px;background:#f9f9f9;padding:10px;border-radius:6px;border:1px solid var(--border);text-align:center">
        <div class="dim" style="font-size:11px;text-transform:uppercase">Valuation Summary</div>
        <div class="amt" style="font-size:18px;color:var(--text)">&#8369;${fmt(val)}</div>
      </div>
    </div>
    <div class="sp-section">
      <div class="sp-section-label">Warehouse Availability</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Warehouse</th><th>Qty On Hand</th><th>Allocated</th><th>Available</th></tr></thead>
          <tbody>
            <tr><td>WH-MNL (Manila Main)</td><td class="mono" style="text-align:center">${Math.floor(i.qty*0.6)}</td><td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">${Math.floor(i.qty*0.6)}</td></tr>
            <tr><td>WH-QC (Quezon City)</td><td class="mono" style="text-align:center">${Math.ceil(i.qty*0.4)}</td><td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">${Math.ceil(i.qty*0.4)}</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="showToast('Edit mode...')">Edit Demo Item</button><button class="btn btn-sm btn-primary" onclick="openInStockItems(JSON.parse(localStorage.getItem('inventoryDemoData')))">Back</button>`;
  openCenterModal('Item Profile: ' + i.desc, html, actions, {width: '800px'});
}

function openInWarehouseDetails(data) {
  const rows = data.stockItems.map(i=>`<tr>
    <td class="mono">${i.code}</td><td>${i.desc}</td><td>WH-MNL</td>
    <td class="mono" style="text-align:center">${i.qty}</td><td class="mono" style="text-align:center">${i.qty}</td>
    <td class="mono" style="text-align:center">0</td><td class="mono" style="text-align:center">${i.reorder}</td><td>${badge(i.status)}</td>
    <td style="text-align:center;white-space:nowrap"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Adjusting...')">Adjust</button> <button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Transferring...')">Transfer</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table>
    <thead><tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th style="text-align:center">Reorder Pt</th><th>Status</th><th style="text-align:center">Action</th></tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('wh-details.csv', [])">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Item Warehouse Details', html, actions, {width: '1100px'});
}

function openInNonStockItems(data) {
  const rows = data.nonStockItems.map(i=>`<tr>
    <td class="mono">${i.code}</td><td><strong>${i.desc}</strong></td><td>${i.cat}</td>
    <td class="mono">${i.account}</td><td class="amt">&#8369;${fmt(i.cost)}</td><td>${badge(i.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing details...')">View Details</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>Item Code</th><th>Description</th><th>Category</th><th>Expense Account</th><th>Standard Cost</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('non-stock.csv', [])">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Non-Stock Items', html, actions, {width: '850px'});
}

function openInWarehouses(data) {
  const rows = data.warehouses.map(w=>`<tr>
    <td class="mono">${w.id}</td><td><strong>${w.name}</strong></td><td>${w.branch}</td><td>${w.loc}</td>
    <td>${w.manager}</td><td class="mono" style="text-align:center">${w.cap}</td><td class="mono" style="text-align:center">${w.util}%</td><td>${badge(w.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing warehouse...')">View</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>WH ID</th><th>Warehouse Name</th><th>Branch</th><th>Location</th><th>Manager</th><th style="text-align:center">Capacity</th><th style="text-align:center">Utilization</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('warehouses.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).warehouses)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Warehouses', html, actions, {width: '1000px'});
}

function openInBuildings(data) {
  const rows = data.warehouseBuildings.map(b=>`<tr>
    <td class="mono">${b.id}</td><td><strong>${b.name}</strong></td><td>${b.wh}</td><td>${b.loc}</td>
    <td>${b.area}</td><td class="mono" style="text-align:center">${b.zones}</td><td>${badge(b.status)}</td>
    <td style="text-align:center"><button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="showToast('Viewing building...')">View</button></td>
  </tr>`).join('');
  const html = `<div class="table-wrap"><table><thead><tr><th>Bldg ID</th><th>Building Name</th><th>Warehouse</th><th>Location</th><th>Floor Area</th><th style="text-align:center">Zones</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('buildings.csv', JSON.parse(localStorage.getItem('inventoryDemoData')).warehouseBuildings)">Export CSV</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Warehouse Buildings', html, actions, {width: '900px'});
}

// ======================= PHYSICAL INVENTORY =======================
function openInPrepareCount(data) {
  const whOpts = data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  const rows = data.stockItems.map(i=>`<tr>
    <td class="mono">${i.code}</td><td>${i.desc}</td><td>WH-MNL</td>
    <td class="mono" style="text-align:center">${i.qty}</td><td>${badge('Uncounted')}</td>
  </tr>`).join('');
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Warehouse</label><select>${whOpts}</select></div>
      <div class="demo-form-row"><label>Count Date</label><input type="date" value="2026-05-30"></div>
      <div class="demo-form-row"><label>Item Class</label><select><option>All</option><option>Motor Unit</option></select></div>
      <div class="demo-form-row"><label>Count Type</label><select><option>Full Physical</option><option>Cycle Count</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end"><label style="display:flex;align-items:center;gap:6px"><input type="checkbox" checked> Freeze Inventory</label></div>
      <div class="demo-form-row"><label>Assigned Counter</label><input type="text" value="Warehouse Team"></div>
      <div class="demo-form-row" style="grid-column:1/-1"><label>Notes</label><input type="text" placeholder="End of month physical count"></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table><thead><tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">System Qty</th><th>Count Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runInPrepareCount(this)">Generate Count Sheet</button>
    <button class="btn btn-sm" onclick="showToast('Printing Count Sheet...')">Print Count Sheet</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Prepare Physical Count', html, actions, {width: '950px'});
}

function runInPrepareCount(btn) {
  setButtonLoading(btn, 'Generating...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    const cntNo = 'CNT-' + String(data.physicalCounts.length + 101);
    const lines = data.stockItems.map(i=>({code:i.code, desc:i.desc, sysQty:i.qty, countedQty: 0, variance: 0, status: 'Pending Entry'}));
    data.physicalCounts.unshift({
      no: cntNo, date: '2026-05-30', wh: 'WH-MNL', lines, status: 'Generated'
    });
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    resetButtonLoading(btn);
    btn.textContent = 'Count Sheet Generated';
    btn.disabled = true;
    showToast('Demo physical count sheet generated successfully.');
  }, 1000);
}

function openInPhysicalCount(data) {
  const cnt = data.physicalCounts[0];
  if(!cnt) {
    openCenterModal('Physical Inventory Count', '<div style="padding:40px;text-align:center;color:var(--text-tertiary)">No count sheets generated yet. Please prepare one first.</div>', '<button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>');
    return;
  }
  
  const rows = cnt.lines.map(l=>`<tr>
    <td class="mono">${l.code}</td><td>${l.desc}</td>
    <td class="mono" style="text-align:center">${l.sysQty}</td>
    <td class="mono" style="text-align:center;background:var(--bg-light);font-weight:bold">${l.countedQty}</td>
    <td class="mono" style="text-align:center;color:${l.variance<0?'var(--red)':'var(--text)'}">${l.variance}</td>
    <td>${badge(l.status)}</td>
    <td style="text-align:center;white-space:nowrap">
      ${cnt.status!=='Posted' ? `<button class="btn btn-sm" style="font-size:10px;padding:2px 6px" onclick="enterInCount('${l.code}')">Enter Count</button>` : ''}
    </td>
  </tr>`).join('');
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Count Sheet No.</label><input type="text" value="${cnt.no}" readonly></div>
      <div class="demo-form-row"><label>Count Date</label><input type="date" value="${cnt.date}" readonly></div>
      <div class="demo-form-row"><label>Warehouse</label><input type="text" value="${cnt.wh}" readonly></div>
      <div class="demo-form-row"><label>Overall Status</label><input type="text" value="${cnt.status}" readonly></div>
    </div>
    <div class="table-wrap">
      <table><thead><tr><th>Item Code</th><th>Description</th><th style="text-align:center">Sys Qty</th><th style="text-align:center">Counted</th><th style="text-align:center">Variance</th><th>Status</th><th style="text-align:center">Action</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    ${cnt.status!=='Posted' ? `<button class="btn btn-sm btn-primary" onclick="approveInCount(this)">Approve Count</button>` : ''}
    ${cnt.status==='Approved' ? `<button class="btn btn-sm btn-primary" onclick="postInCountAdjustment(this)">Post Adjustment</button>` : ''}
    <button class="btn btn-sm" onclick="exportRowsToCsv('physical-count.csv', [])">Export CSV</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Physical Inventory Count: ' + cnt.no, html, actions, {width: '1000px'});
}

function enterInCount(code) {
  let qty = prompt('Enter physical counted quantity for ' + code + ':');
  if(qty !== null) {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let cnt = data.physicalCounts[0];
    let line = cnt.lines.find(l=>l.code===code);
    if(line) {
      line.countedQty = parseInt(qty)||0;
      line.variance = line.countedQty - line.sysQty;
      line.status = 'Counted';
      localStorage.setItem('inventoryDemoData', JSON.stringify(data));
      openInPhysicalCount(data);
    }
  }
}

function approveInCount(btn) {
  let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
  if(data.physicalCounts[0]) {
    data.physicalCounts[0].status = 'Approved';
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Physical count approved.');
    openInPhysicalCount(data);
  }
}

function postInCountAdjustment(btn) {
  setButtonLoading(btn, 'Posting...');
  setTimeout(() => {
    let data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let cnt = data.physicalCounts[0];
    cnt.status = 'Posted';
    
    // Auto generate adjustments for variances
    cnt.lines.filter(l=>l.variance !== 0).forEach(l => {
      data.adjustments.unshift({
        no: 'ADJ-VAR-' + String(data.adjustments.length + 100),
        date: cnt.date, wh: cnt.wh, item: l.code, lot: '',
        type: l.variance > 0 ? 'Increase' : 'Decrease',
        qty: Math.abs(l.variance),
        reason: 'Physical Count Variance',
        status: 'Released'
      });
      let item = data.stockItems.find(i=>i.code===l.code);
      if(item) item.qty = l.countedQty; // fix stock to counted qty
    });
    
    localStorage.setItem('inventoryDemoData', JSON.stringify(data));
    showToast('Demo adjustments posted successfully.');
    openInPhysicalCount(data);
  }, 1000);
}

// ======================= PROCESSES =======================
function openInProcessReleaseDocs(data) {
  const rows = data.adjustments.filter(a=>a.status==='Pending').map(a=>`<tr>
    <td class="mono">${a.no}</td><td>Adjustment</td><td>${a.wh}</td>
    <td>${a.item}</td><td class="mono" style="text-align:center">${a.qty}</td><td>${badge('Pending')}</td>
  </tr>`).join('') || '<tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No pending documents to release</td></tr>';
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Document Type</label><select><option>All</option><option>Receipts</option><option>Issues</option><option>Adjustments</option></select></div>
      <div class="demo-form-row"><label>Warehouse</label><select><option>All Branches</option></select></div>
      <div class="demo-form-row"><label>Status</label><select><option>Unreleased</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table><thead><tr><th>Document No.</th><th>Type</th><th>Warehouse</th><th>Item</th><th style="text-align:center">Qty</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runInReleaseDocs(this)">Release Documents</button>
    <button class="btn btn-sm" onclick="showToast('Printing Release Log...')">Print Release Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Release IN Documents', html, actions, {width: '850px'});
}

function runInReleaseDocs(btn) {
  setButtonLoading(btn, 'Releasing...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Documents Released';
    btn.disabled = true;
    showToast('Demo inventory documents released successfully.');
  }, 1000);
}

function openInProcessClosePeriod(data) {
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Financial Period</label><select><option>05-2026</option></select></div>
      <div class="demo-form-row"><label>Warehouse</label><select><option>All</option></select></div>
      <div class="demo-form-row"><label>Module</label><select><option>Inventory</option></select></div>
      <div class="demo-form-row"><label>Closing Option</label><select><option>Soft Close</option></select></div>
    </div>
    <div class="table-wrap" style="max-height:250px;overflow-y:auto">
      <table>
        <thead><tr><th>Validation Item</th><th>Result</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>Unreleased inventory documents</td><td class="dim" id="in-val-1">-</td><td id="in-stat-1">${badge('Pending')}</td></tr>
          <tr><td>Negative stock check</td><td class="dim" id="in-val-2">-</td><td id="in-stat-2">${badge('Pending')}</td></tr>
          <tr><td>Physical count variance check</td><td class="dim" id="in-val-3">-</td><td id="in-stat-3">${badge('Pending')}</td></tr>
          <tr><td>Goods in transit validation</td><td class="dim" id="in-val-4">-</td><td id="in-stat-4">${badge('Pending')}</td></tr>
          <tr><td>Inventory valuation check</td><td class="dim" id="in-val-5">-</td><td id="in-stat-5">${badge('Pending')}</td></tr>
          <tr><td>GL posting check</td><td class="dim" id="in-val-6">-</td><td id="in-stat-6">${badge('Pending')}</td></tr>
        </tbody>
      </table>
    </div>
  `;
  const actions = `
    <button class="btn btn-sm btn-primary" onclick="runInValidatePeriod(this)" id="in-val-btn">Validate Period</button>
    <button class="btn btn-sm btn-primary" onclick="runInClosePeriod(this)" id="in-clo-btn" disabled>Close Period</button>
    <button class="btn btn-sm" onclick="showToast('Printing Log...')">Print Closing Log</button>
    <button class="btn btn-sm" onclick="closeCenterModal()">Close</button>
  `;
  openCenterModal('Close Financial Periods (IN)', html, actions, {width: '800px'});
}

function runInValidatePeriod(btn) {
  setButtonLoading(btn, 'Validating...');
  setTimeout(() => {
    document.getElementById('in-val-1').textContent = '0 Documents'; document.getElementById('in-stat-1').innerHTML = badge('OK');
    document.getElementById('in-val-2').textContent = 'Passed'; document.getElementById('in-stat-2').innerHTML = badge('OK');
    document.getElementById('in-val-3').textContent = 'Posted'; document.getElementById('in-stat-3').innerHTML = badge('OK');
    document.getElementById('in-val-4').textContent = '1 Transit'; document.getElementById('in-stat-4').innerHTML = badge('OK');
    document.getElementById('in-val-5').textContent = 'Balanced'; document.getElementById('in-stat-5').innerHTML = badge('OK');
    document.getElementById('in-val-6').textContent = 'Posted'; document.getElementById('in-stat-6').innerHTML = badge('OK');
    resetButtonLoading(btn);
    btn.textContent = 'Validated';
    btn.disabled = true;
    document.getElementById('in-clo-btn').disabled = false;
    showToast('Period validation complete. Ready to close.');
  }, 1500);
}

function runInClosePeriod(btn) {
  setButtonLoading(btn, 'Closing Period...');
  setTimeout(() => {
    resetButtonLoading(btn);
    btn.textContent = 'Period Closed';
    btn.disabled = true;
    showToast('Demo inventory financial period closed successfully.');
  }, 1000);
}

// ======================= INQUIRIES =======================
function openInInquiryModal(action, data) {
  let content = '';
  let width = '1000px';
  
  if (action === 'Inventory Summary') {
    const rows = data.stockItems.map(i=>`<tr><td class="mono">${i.code}</td><td>${i.desc}</td><td>${i.class}</td><td class="mono" style="text-align:center">${i.qty}</td><td class="mono" style="text-align:center">${i.qty}</td><td class="mono" style="text-align:center">0</td><td class="amt">&#8369;${fmt(i.qty*i.cost)}</td><td>${badge(i.status)}</td></tr>`).join('');
    content = `<div class="table-wrap"><table><thead><tr><th>Item Code</th><th>Description</th><th>Item Class</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th>Inventory Value</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  } 
  else if (action === 'Storage Summary') {
    const rows = data.warehouses.map(w=>`<tr><td>${w.id}</td><td class="mono" style="text-align:center">${w.cap}</td><td class="mono" style="text-align:center">${Math.floor(w.cap*(w.util/100))}</td><td class="mono" style="text-align:center">${w.cap - Math.floor(w.cap*(w.util/100))}</td><td class="mono" style="text-align:center">${w.util}%</td><td>${badge(w.status)}</td></tr>`).join('');
    content = `<div class="table-wrap"><table><thead><tr><th>Warehouse</th><th style="text-align:center">Capacity</th><th style="text-align:center">Used</th><th style="text-align:center">Available</th><th style="text-align:center">Utilization %</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  }
  else if (action === 'Inventory Allocation Details') {
    content = `<div class="table-wrap"><table><thead><tr><th>Item</th><th>Warehouse</th><th>Sales Order No.</th><th style="text-align:center">Allocated Qty</th><th>Date</th><th>Status</th></tr></thead><tbody><tr><td colspan="6" style="text-align:center;color:var(--text-tertiary)">No allocations</td></tr></tbody></table></div>`;
  }
  else if (action === 'Inventory Transactions by Account') {
    content = `<div class="table-wrap"><table><thead><tr><th>GL Account</th><th>Account Name</th><th>Transaction Type</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead><tbody><tr><td class="mono">12000</td><td>Inventory Asset</td><td>Receipt</td><td class="amt">&#8369;325,000</td><td class="amt">-</td><td class="amt">&#8369;325,000</td></tr></tbody></table></div>`;
  }
  else if (action === 'Inventory Lot/Serial History') {
    content = `<div class="table-wrap"><table><thead><tr><th>Lot/Serial No.</th><th>Item</th><th>Warehouse</th><th>Transaction Date</th><th>Type</th><th>Ref No.</th><th>Status</th></tr></thead><tbody><tr><td class="mono">LOT-009A</td><td>Honda Click 125i</td><td>WH-MNL</td><td>2026-05-10</td><td>Receipt</td><td class="mono">REC-2001</td><td>${badge('In Stock')}</td></tr></tbody></table></div>`;
  }
  else if (action === 'Inventory by Item Class') {
    content = `<div class="table-wrap"><table><thead><tr><th>Item Class</th><th style="text-align:center">Item Count</th><th style="text-align:center">On Hand Qty</th><th>Inventory Value</th><th>Reorder Alerts</th></tr></thead><tbody><tr><td>Motor Unit</td><td class="mono" style="text-align:center">3</td><td class="mono" style="text-align:center">43</td><td class="amt">&#8369;3,215,000</td><td class="mono" style="text-align:center;color:var(--red)">0</td></tr></tbody></table></div>`;
  }
  else if (action === 'Dead Stock') {
    content = `<div class="table-wrap"><table><thead><tr><th>Item</th><th>Warehouse</th><th style="text-align:center">Qty</th><th>Last Movement Date</th><th style="text-align:center">Days Without Movement</th><th>Action</th></tr></thead><tbody><tr><td>Brake Pad Set Old</td><td>WH-CEB</td><td class="mono" style="text-align:center">25</td><td>2025-01-10</td><td class="mono" style="text-align:center;color:var(--red)">491</td><td>Mark for clearance</td></tr></tbody></table></div>`;
  }
  else if (action === 'Intercompany Goods in Transit') {
    const rows = data.transfers.filter(t=>t.status==='In Transit').map(t=>`<tr><td class="mono">${t.no}</td><td>${t.from}</td><td>${t.to}</td><td>${t.item}</td><td class="mono" style="text-align:center">${t.qty}</td><td>${t.date}</td><td>${badge(t.status)}</td></tr>`).join('') || '<tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No goods in transit</td></tr>';
    content = `<div class="table-wrap"><table><thead><tr><th>Transfer No.</th><th>Source</th><th>Destination</th><th>Item</th><th style="text-align:center">Qty</th><th>Ship Date</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table></div>`;
  }
  else if (action === 'Intercompany Returned Goods in Transit') {
    content = `<div class="table-wrap"><table><thead><tr><th>Return No.</th><th>Source</th><th>Destination</th><th>Item</th><th style="text-align:center">Qty</th><th>Return Date</th><th>Status</th></tr></thead><tbody><tr><td colspan="7" style="text-align:center;color:var(--text-tertiary)">No returned goods in transit</td></tr></tbody></table></div>`;
  }

  const wrapper = `
    <div style="margin-bottom:12px;display:flex;gap:10px">
      <input type="text" placeholder="Search..." style="padding:6px 10px;border:1px solid var(--border);border-radius:4px;flex:1">
      <button class="btn btn-sm">Search</button>
    </div>
    ${content}
  `;
  const actions = `<button class="btn btn-sm" onclick="exportRowsToCsv('in-inquiry.csv', [])">Export CSV</button><button class="btn btn-sm" onclick="showToast('Printing...')">Print</button><button class="btn btn-sm btn-primary" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Inquiry: ' + action, wrapper, actions, {width});
}

// ======================= REPORTS =======================
function openInReportModal(reportName, data) {
  const whOpts = `<option value="">All Warehouses</option>` + data.warehouses.map(w=>`<option value="${w.id}">${w.name}</option>`).join('');
  const classOpts = `<option value="">All</option><option>Motor Unit</option><option>Accessories</option>`;
  
  const html = `
    <div class="demo-form-grid" style="margin-bottom:16px">
      <div class="demo-form-row"><label>Date From</label><input type="date" value="2026-05-01"></div>
      <div class="demo-form-row"><label>Date To</label><input type="date" value="2026-05-31"></div>
      <div class="demo-form-row"><label>Warehouse</label><select>${whOpts}</select></div>
      <div class="demo-form-row"><label>Item Class</label><select>${classOpts}</select></div>
      <div class="demo-form-row"><label>Status</label><select><option>All</option></select></div>
      <div class="demo-form-row" style="display:flex;align-items:flex-end">
        <button class="btn btn-sm btn-primary" style="width:100%" onclick="runInDemoReport(this, '${reportName}')">Run Report</button>
      </div>
    </div>
    <div class="table-wrap" id="in-report-body" style="min-height:200px">
      <div style="padding:40px;text-align:center;color:var(--text-tertiary)">Select filters and click Run Report</div>
    </div>
  `;
  const actions = `<button class="btn btn-sm" onclick="showToast('Exported CSV')">Export CSV</button><button class="btn btn-sm" onclick="showToast('Print Preview loaded')">Print Preview</button><button class="btn btn-sm" onclick="showToast('Email sent!')">Email Report</button><button class="btn btn-sm" onclick="closeCenterModal()">Close</button>`;
  openCenterModal('Report: ' + reportName, html, actions, {width:'min(1050px, calc(100vw - 48px))'});
}

function runInDemoReport(btn, reportName) {
  setButtonLoading(btn, 'Running...');
  setTimeout(() => {
    const data = JSON.parse(localStorage.getItem('inventoryDemoData'));
    let rows = '';
    let thead = '';
    
    if (reportName === 'Inventory Balance') {
      thead = `<tr><th>Item Code</th><th>Description</th><th>Warehouse</th><th style="text-align:center">On Hand</th><th style="text-align:center">Available</th><th style="text-align:center">Allocated</th><th>Status</th></tr>`;
      rows = data.stockItems.map(i=>`<tr><td class="mono">${i.code}</td><td>${i.desc}</td><td>WH-MNL</td><td class="mono" style="text-align:center">${i.qty}</td><td class="mono" style="text-align:center">${i.qty}</td><td class="mono" style="text-align:center">0</td><td>${badge(i.status)}</td></tr>`).join('');
    } else if (reportName === 'Inventory Valuation') {
      thead = `<tr><th>Item Code</th><th>Description</th><th style="text-align:center">Qty</th><th>Unit Cost</th><th>Total Value</th><th>Valuation Method</th></tr>`;
      rows = data.stockItems.map(i=>`<tr><td class="mono">${i.code}</td><td>${i.desc}</td><td class="mono" style="text-align:center">${i.qty}</td><td class="amt">&#8369;${fmt(i.cost)}</td><td class="amt" style="font-weight:bold">&#8369;${fmt(i.qty*i.cost)}</td><td>Average Cost</td></tr>`).join('');
    } else if (reportName === 'Inventory Register') {
      thead = `<tr><th>Date</th><th>Reference No.</th><th>Type</th><th>Item</th><th>Warehouse</th><th style="text-align:center">Qty In</th><th style="text-align:center">Qty Out</th><th>Balance</th></tr>`;
      rows = `<tr><td class="dim">2026-05-10</td><td class="mono">REC-2001</td><td>Receipt</td><td>Honda Click 125i</td><td>WH-MNL</td><td class="mono" style="text-align:center;color:var(--green)">+5</td><td class="mono" style="text-align:center">-</td><td class="mono">15</td></tr>`;
    } else if (reportName === 'Goods in Transit') {
      thead = `<tr><th>Transfer No.</th><th>From WH</th><th>To WH</th><th>Item</th><th style="text-align:center">Qty</th><th>Ship Date</th><th>Exp Receipt Date</th><th>Status</th></tr>`;
      rows = data.transfers.filter(t=>t.status==='In Transit').map(t=>`<tr><td class="mono">${t.no}</td><td>${t.from}</td><td>${t.to}</td><td>${t.item}</td><td class="mono" style="text-align:center">${t.qty}</td><td class="dim">${t.date}</td><td class="dim">2026-05-18</td><td>${badge(t.status)}</td></tr>`).join('') || '<tr><td colspan="8" style="text-align:center;color:var(--text-tertiary)">No goods in transit</td></tr>';
    } else if (reportName === 'Lot/Serial Numbers') {
      thead = `<tr><th>Lot/Serial No.</th><th>Item</th><th>Warehouse</th><th>Received Date</th><th>Current Status</th><th>Reference No.</th></tr>`;
      rows = `<tr><td class="mono" style="font-weight:bold">ENG-X890123</td><td>Honda Click 125i</td><td>WH-MNL</td><td>2026-05-10</td><td>${badge('Available')}</td><td class="mono">REC-2001</td></tr>`;
    } else {
      thead = `<tr><th>Record No.</th><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>`;
      rows = `<tr><td class="mono">REC-001</td><td class="dim">2026-05-16</td><td>Demo Report Row</td><td class="amt">&#8369;10,000.00</td><td>${badge('Active')}</td></tr>`;
    }
    
    document.getElementById('in-report-body').innerHTML = `<table><thead>${thead}</thead><tbody>${rows}</tbody></table>`;
    resetButtonLoading(btn);
    showToast('Demo inventory report generated successfully.');
  }, 800);
}
// --- END INVENTORY MODULE DEMO ---

function doLogout(){
  document.getElementById('login-screen').style.display='flex';
  document.getElementById('app').style.display='none';
  document.getElementById('l-user').value='';
  document.getElementById('l-pass').value='';
}
