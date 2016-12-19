import os

command ="""ALTER TABLE trlp_%s
  ALTER COLUMN wkb_geometry TYPE geometry(LINESTRINGZ,31466)
    USING ST_SetSRID(wkb_geometry,31466);
    """


for root, dir, files in os.walk(u"."):
        for gmlFile in files:
                if gmlFile.endswith(".gml"):
                        print command % (gmlFile[:gmlFile.index(".gml")-1] )
