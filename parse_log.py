import re

users = {}
with open('log') as handle:
    for line in handle.readlines():
        m = re.search('q=%2B(\d+)', line)

        if m:
            user = m.group(1)
            if user not in users:
                users[user] = True
                print user
