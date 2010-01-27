
cd \pos\installation\mysql\is4c_log\tables

mysql -e "source activitylog.table"
mysql -e "source dtransactions.table"
mysql -e "source suspended.table"


cd \pos\installation\mysql\is4c_log\views\


mysql -e "source dlog.viw"
mysql -e "source tendertape.viw"
mysql -e "source buspasstotals.viw"
mysql -e "source cctenders.viw"
mysql -e "source cctendertotal.viw"
mysql -e "source cktenders.viw"
mysql -e "source cktendertotal.viw"
mysql -e "source dctenders.viw"
mysql -e "source dctendertotal.viw"
mysql -e "source memchargebalance.viw"
mysql -e "source memchargetotals.viw"
mysql -e "source mitenders.viw"
mysql -e "source mitendertotal.viw"
mysql -e "source suspendedtoday.viw"



cd \pos\installation\mysql\is4c_op\tables\


mysql -e "source batches.table"
mysql -e "source batchList.table"
mysql -e "source chargecode.table"
mysql -e "source couponcodes.table"
mysql -e "source custdata.table"
mysql -e "source departments.table"
mysql -e "source employees.table"
mysql -e "source error_log.table"
mysql -e "source globalvalues.table"
mysql -e "source likecodes.table"
mysql -e "source meminfo.table"
mysql -e "source memtype.table"
mysql -e "source newMembers.table"
mysql -e "source products.table"
mysql -e "source prodUpdate.table"
mysql -e "source promomsgs.table"
mysql -e "source subdepts.table"
mysql -e "source tenders.table"
mysql -e "source UNFI.table"
mysql -e "source upclike.table"


cd \pos\installation\mysql\is4c_op\views\

mysql -e "source chargecodeview.viw"
mysql -e "source memchargebalance.viw"
mysql -e "source subdeptIndex.viw"
mysql -e "source volunteerDiscounts.viw"


cd \pos\installation\mysql\is4c_op\data\


mysql -e "source batches.insert"
mysql -e "source batchList.insert"
mysql -e "source custdata.insert"
mysql -e "source departments.insert"
mysql -e "source employees.insert"
mysql -e "source globalvalues.insert"
mysql -e "source memtype.insert"
mysql -e "source products.insert"
mysql -e "source subdepts.insert"
mysql -e "source tenders.insert"
