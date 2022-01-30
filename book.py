#!/usr/bin/python3
import sqlite3
import binascii
from prettytable import from_db_cursor,PrettyTable
import os,glob,sys
class BookController:
    connection:None
    def __init__(self):
        con=sqlite3.connect('/home/chipskein/Books/books.db')
        con.execute('PRAGMA FOREIGN_KEY=ON')
        con.commit()
        self.connection=con 
    def add(self,bookname):
        con=self.connection
        binary=f'{bookname}'.encode('utf-8')
        id=binascii.crc32(binary)
        if(con):
            con.execute(f'INSERT OR IGNORE INTO books(identifier,name) VALUES({id},"{bookname}")')
            con.commit()
        else: 
            print('Database Connection Failed')
    def remove(self,bookname):
        con=self.connection
        binary=f'{bookname}'.encode('utf-8')
        id=binascii.crc32(binary)
        if(con):
            con.execute(f'DELETE FROM books WHERE identifier={id}')
            con.commit()
        else: 
            print('Database Connection Failed')
    def update(self,bookname,page):
        con=self.connection
        binary=f'{bookname}'.encode('utf-8')
        id=binascii.crc32(binary)
        con.execute(f'UPDATE books SET last_page={page},last_update=CURRENT_TIMESTAMP WHERE identifier={id}')
        con.commit()
    def CreateTable(self):
        con=self.connection
        create_table_query= '''
            CREATE TABLE IF NOT EXISTS books(
                identifier integer not null,
                name varchar(400) not null,
                last_page integer default 0,
                register_time default CURRENT_TIMESTAMP,
                last_update default CURRENT_TIMESTAMP,
                PRIMARY KEY(identifier)    
            )
        '''
        con.execute(create_table_query)
        con.commit()
    def getAll(self): 
        con=self.connection
        cursor=con.cursor()
        cursor.execute('SELECT substr( name, 0,40 ) as name ,last_page,last_update FROM books')
        tbl=from_db_cursor(cursor)
        print(tbl)
    def get(self,bookname):
        con=self.connection
        binary=f'{bookname}'.encode('utf-8')
        id=binascii.crc32(binary)
        cursor=con.cursor()
        cursor.execute(f'SELECT substr( name, 0,40 ) as name ,last_page,last_update FROM books where identifier={id}')
        tbl=from_db_cursor(cursor)
        print(tbl)
        return
    def addAllFromDir(self):
        os.chdir('./')
        files=glob.glob('*.pdf')
        for file in files:
            self.add(file)
    def Help(self):
        tbl=PrettyTable()
        tbl.field_names = ["Commands", "argv","DO"]
        tbl.add_rows(
            [
                ["add $argv1","bookname","Add new book"],
                ["remove $argv1", "bookname","Remove book"],
                ["update $argv1 $argv2","bookname,page","update Book"],
                ["get $argv1", "bookname", "get book"],
                ["getall", "NONE","Get all books"],
                ["addall", "NONE", "add all books"],
                ["help", "NONE", "print help"],
                ["backup", "NONE", "dump sql script to backup.sql"],
            ]
        )
        print(tbl)
    def backup(self):
        con=self.connection
        f=open('backup.sql','w')
        for line in con.iterdump():
            f.write('%s\n' % line)
        f.close()
argv=sys.argv[1:]
"""Operation"""
ope=argv[0] if len(argv)>0 else None 
"""Bookname"""
ope2=argv[1] if len(argv)>1 else None 
"""Page"""
ope3=argv[2] if len(argv)>2 else None 
Controller=BookController()
"""Verify if argv[] len is not empty"""
if(ope is not None):
    if(ope2 is not None):
        if(ope3 is not None):
            if(ope=="update"):
                Controller.update(ope2,ope3)
        else:
            if(ope=="add"):
                Controller.add(ope2)
            if(ope=="remove"):
                Controller.remove(ope2)
            if(ope=="get"):
                Controller.get(ope2)        
    
    else:
        if(ope=="getall"):
            Controller.getAll()
        if(ope=="addall"):
            Controller.addAllFromDir()
        if(ope=="help"):
            Controller.Help()
        if(ope=="backup"):
            Controller.backup()
else:
    """Print Help"""
    Controller.Help()
