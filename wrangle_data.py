from bs4 import BeautifulSoup
import pandas as pd


raw_html = open("raw_html.html")
html = BeautifulSoup(raw_html, 'html.parser')
df  = pd.DataFrame(columns=['Planet','Mission','Rotation','Item','Droprate %','Droprate Category'])


mission_table = html.find("h3",{"id":"missionRewards"}).findNext()
curPlanet = ""
curMission = ""
curRot = ""
for row in mission_table.findChildren(recursive=False):
    rw = row.text
    if rw != "":
#        try:
        if '/' in rw:
            curPlanet = rw.split("/")[0]
            curMission = rw.split("/")[1]
            curRot = ""
        elif rw.split()[0] == "Rotation":
            curRot = rw.split()[0]
        else:
            r = row.select('td')
            item = r[0].text
            category = r[1].text.split()[0]
            percent = ''.join(filter(lambda x: x in '.0123456789', r[1].text.split()[1]))
            df = df.append(pd.DataFrame(columns=df.columns, data=[[curPlanet,curMission,curRot,item,percent,category]]))
#           except:
#               print("r", row)
df.head()
df.to_csv('Mission_data.csv')