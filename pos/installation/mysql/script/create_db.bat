cd \pos\installation\mysql\translog\tables

 mysql -e "source activities.table"
 mysql -e "source activitylog.table"
 mysql -e "source activitytemplog.table"
 mysql -e "source alog.table"
 mysql -e "source dtransactions.table"
 mysql -e "source localtemptrans.table"
 mysql -e "source localtrans.table"
 mysql -e "source localtransarchive.table"
 mysql -e "source suspended.table"


cd ..\views

 mysql -e "source localtranstoday.viw"
 mysql -e "source suspendedtoday.viw"
 mysql -e "source suspendedlist.viw"


 mysql -e "source lttsummary.viw"
 mysql -e "source lttsubtotals.viw"
 mysql -e "source subtotals.viw"

 mysql -e "source ltt_receipt.viw"
 mysql -e "source receipt.viw"

 mysql -e "source rp_ltt_receipt.viw"
 mysql -e "source rp_receipt_header.viw"
 mysql -e "source rp_receipt.viw"
 mysql -e "source rp_list.viw"

 mysql -e "source screendisplay.viw"

 mysql -e "source memdiscountadd.viw"
 mysql -e "source memdiscountremove.viw"
 mysql -e "source staffdiscountadd.viw"
 mysql -e "source staffdiscountremove.viw"

 mysql -e "source memchargetotals.viw"

cd \pos\installation\mysql\opdata\tables


 mysql -e "source chargecode.table"
 mysql -e "source couponcodes.table"
 mysql -e "source custdata.table"
 mysql -e "source departments.table"
 mysql -e "source employees.table"
 mysql -e "source globalvalues.table"
 mysql -e "source products.table"
 mysql -e "source promomsgs.table"
 mysql -e "source tenders.table"

cd ..\data

 mysql -e "source couponcodes.insert"
 mysql -e "source custdata.insert"
 mysql -e "source departments.insert"
 mysql -e "source employees.insert"
 mysql -e "source globalvalues.insert"
 mysql -e "source products.insert"
 mysql -e "source tenders.insert"

cd ..\views

 mysql -e "source chargecodeview.viw"
 mysql -e "source memchargebalance.viw"


cd \pos\installation\mysql\is4c_op\tables

 mysql -e "source couponcodes.table"
 mysql -e "source custdata.table"
 mysql -e "source chargecode.table"
 mysql -e "source departments.table"
 mysql -e "source employees.table"
 mysql -e "source products.table"
 mysql -e "source tenders.table"

cd \pos\installation\mysql\script
